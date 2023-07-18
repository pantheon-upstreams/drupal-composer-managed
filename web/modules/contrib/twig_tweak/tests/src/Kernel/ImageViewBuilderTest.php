<?php

namespace Drupal\Tests\twig_tweak\Kernel;

use Drupal\Core\Cache\Cache;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DrupalKernel;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;
use Drupal\responsive_image\Entity\ResponsiveImageStyle;
use Symfony\Component\HttpFoundation\Request;

/**
 * A test for ImageViewBuilderTest.
 *
 * @group twig_tweak
 */
final class ImageViewBuilderTest extends AbstractTestCase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'twig_tweak',
    'twig_tweak_test',
    'user',
    'system',
    'file',
    'image',
    'responsive_image',
    'breakpoint',
  ];

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    // Add file_private_path setting.
    $request = Request::create('/');
    $site_path = DrupalKernel::findSitePath($request);
    $this->setSetting('file_private_path', $site_path . '/private');

    $this->installEntitySchema('file');
    $this->installSchema('file', 'file_usage');
    ImageStyle::create(['name' => 'large'])->save();
    ResponsiveImageStyle::create(['id' => 'wide'])->save();
  }

  /**
   * {@inheritdoc}
   */
  public function register(ContainerBuilder $container) {
    parent::register($container);
    $container->register('stream_wrapper.private', 'Drupal\Core\StreamWrapper\PrivateStream')
      ->addTag('stream_wrapper', ['scheme' => 'private']);
  }

  /**
   * Test callback.
   */
  public function testImageViewBuilder(): void {

    $view_builder = $this->container->get('twig_tweak.image_view_builder');

    /** @var \Drupal\file\FileInterface $public_image */
    $public_image = File::create(['uri' => 'public://ocean.jpg']);
    $public_image->save();

    /** @var \Drupal\file\FileInterface $private_image */
    $private_image = File::create(['uri' => 'private://sea.jpg']);
    $private_image->save();

    // -- Without style.
    $build = $view_builder->build($public_image);
    $expected_build = [
      '#uri' => 'public://ocean.jpg',
      '#attributes' => [],
      '#theme' => 'image',
      '#cache' => [
        'contexts' => [
          'user',
          'user.permissions',
        ],
        'tags' => [
          'file:1',
          'tag_for_public://ocean.jpg',
        ],
        'max-age' => 70,
      ],
    ];
    self::assertRenderArray($expected_build, $build);
    self::assertSame('<img src="/files/ocean.jpg" alt="" />', $this->renderPlain($build));

    // -- With style.
    $build = $view_builder->build($public_image, 'large', ['alt' => 'Ocean']);
    $expected_build = [
      '#uri' => 'public://ocean.jpg',
      '#attributes' => ['alt' => 'Ocean'],
      '#theme' => 'image_style',
      '#style_name' => 'large',
      '#cache' => [
        'contexts' => [
          'user',
          'user.permissions',
        ],
        'tags' => [
          'file:1',
          'tag_for_public://ocean.jpg',
        ],
        'max-age' => 70,
      ],
    ];
    self::assertRenderArray($expected_build, $build);
    self::assertSame('<img alt="Ocean" src="/files/styles/large/public/ocean.jpg?itok=abc" />', $this->renderPlain($build));

    // -- With responsive style.
    $build = $view_builder->build($public_image, 'wide', ['alt' => 'Ocean'], TRUE);
    $expected_build = [
      '#uri' => 'public://ocean.jpg',
      '#attributes' => ['alt' => 'Ocean'],
      '#type' => 'responsive_image',
      '#responsive_image_style_id' => 'wide',
      '#cache' => [
        'contexts' => [
          'user',
          'user.permissions',
        ],
        'tags' => [
          'file:1',
          'tag_for_public://ocean.jpg',
        ],
        'max-age' => 70,
      ],
    ];
    self::assertRenderArray($expected_build, $build);
    self::assertSame('<picture><img src="/files/ocean.jpg" alt="Ocean" /></picture>', $this->renderPlain($build));

    // -- Private image with access check.
    $build = $view_builder->build($private_image);
    $expected_build = [
      '#cache' => [
        'contexts' => ['user'],
        'tags' => [
          'file:2',
          'tag_for_private://sea.jpg',
        ],
        'max-age' => 70,
      ],
    ];
    self::assertRenderArray($expected_build, $build);
    self::assertSame('', $this->renderPlain($build));

    // -- Private image without access check.
    $build = $view_builder->build($private_image, NULL, [], FALSE, FALSE);
    $expected_build = [
      '#uri' => 'private://sea.jpg',
      '#attributes' => [],
      '#theme' => 'image',
      '#cache' => [
        'contexts' => [],
        'tags' => ['file:2'],
        'max-age' => Cache::PERMANENT,
      ],
    ];
    self::assertRenderArray($expected_build, $build);
    self::assertSame('<img src="/files/sea.jpg" alt="" />', $this->renderPlain($build));
  }

  /**
   * Renders a render array.
   */
  private function renderPlain(array $build): string {
    $html = $this->container->get('renderer')->renderPlain($build);
    $html = preg_replace('#src=".+/files/#s', 'src="/files/', $html);
    $html = preg_replace('#\?itok=.+"#', '?itok=abc"', $html);
    $html = preg_replace(['#\s{2,}#', '#\n#'], '', $html);
    return rtrim($html);
  }

}
