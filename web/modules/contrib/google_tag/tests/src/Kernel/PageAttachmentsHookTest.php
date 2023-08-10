<?php

declare(strict_types=1);

namespace Drupal\Tests\google_tag\Kernel;

use Drupal\Core\Url;
use Drupal\google_tag\Entity\TagContainer;

/**
 * Tests page attachment hooks.
 *
 * @group google_tag
 */
final class PageAttachmentsHookTest extends GoogleTagTestCase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['google_tag_test'];

  /**
   * Tests that there are no attachments when there's no config.
   */
  public function testNoEmbedOnNoConfig(): void {
    $page = [];
    $this->container
      ->get('main_content_renderer.html')
      ->invokePageAttachmentHooks($page);
    self::assertNotContains('google_tag/gtag', $page['#attached']['library']);
    self::assertNotContains('drupalSettings', $page['#attached']);
  }

  /**
   * Tests that there are valid attachments when there's google_tag configured.
   */
  public function testAttachments(): void {
    TagContainer::create([
      'id' => 'foo',
      // https://developers.google.com/tag-platform/gtagjs/configure#:~:text=What%20is%20a%20tag%20ID%20and%20where%20to%20find%20it%3F
      // @todo need unit test on config entity for this and the methods of default and additional.
      'tag_container_ids' => [
        'GT-XXXXXX',
        'G-XXXXXX',
        'AW-XXXXXX',
        'DC-XXXXXX',
        'UA-XXXXXX',
      ],
      'events' => [
        'route_name' => [],
      ],
    ])->save();

    $page = [];
    $this->container
      ->get('main_content_renderer.html')
      ->invokePageAttachmentHooks($page);
    self::assertEquals([
      'contexts' => [],
      'tags' => $this->container->get('entity_type.manager')->getDefinition('google_tag_container')->getListCacheTags(),
      'max-age' => -1,
    ], $page['#cache']);
    self::assertContains('google_tag/gtag', $page['#attached']['library']);
    self::assertEquals([
      'gtag' => [
        'tagId' => 'GT-XXXXXX',
        'otherIds' => [
          'G-XXXXXX',
          'AW-XXXXXX',
          'DC-XXXXXX',
          'UA-XXXXXX',
        ],
        'events' => [
          [
            'name' => 'route_name',
            'data' => [
              'route_name' => '<none>',
            ],
          ],
        ],
        'additionalConfigInfo' => [],
      ],
    ], $page['#attached']['drupalSettings']);

  }

  /**
   * Tests hook_page_top for google_tag attachments.
   */
  public function testPageTopAttachments(): void {
    TagContainer::create([
      'id' => 'foo',
      'tag_container_ids' => [
        'GTM-XXXXXX',
        'GTM-YYYYYY',
        'GT-XXXXXX',
        'G-XXXXXX',
        'AW-XXXXXX',
        'DC-XXXXXX',
        'UA-XXXXXX',
      ],
    ])->save();

    $page = [];
    $this->container
      ->get('main_content_renderer.html')->buildPageTopAndBottom($page);
    self::assertEquals([
      'google_tag_gtm_iframe' => [
        '#cache' => [
          'contexts' => [],
          'tags' => $this->container->get('entity_type.manager')->getDefinition('google_tag_container')->getListCacheTags(),
          'max-age' => -1,
        ],
        '0' => [
          '#theme' => 'google_tag_gtm_iframe',
          '#url' => Url::fromUri('https://www.googletagmanager.com/ns.html', ['query' => ['id' => 'GTM-XXXXXX']]),
        ],
        '1' => [
          '#theme' => 'google_tag_gtm_iframe',
          '#url' => Url::fromUri('https://www.googletagmanager.com/ns.html', ['query' => ['id' => 'GTM-YYYYYY']]),
        ],
      ],
    ], $page['page_top']
    );
  }

  /**
   * Tests gtm attachments in drupal settings in hook_page_attachments.
   */
  public function testGtmAttachments(): void {
    $allowlist_classes = 'google' . PHP_EOL . 'nonGoogleIframes';
    $blocklist_classes = 'nonGoogleScripts' . PHP_EOL . 'customPixels';
    TagContainer::create([
      'id' => 'foo',
      'tag_container_ids' => [
        'GTM-XXXXXX',
        'GTM-YYYYYY',
        'GT-XXXXXX',
        'G-XXXXXX',
        'AW-XXXXXX',
        'DC-XXXXXX',
        'UA-XXXXXX',
      ],
      'advanced_settings' => [
        'gtm' => [
          'include_classes' => TRUE,
          'allowlist_classes' => $allowlist_classes,
          'blocklist_classes' => $blocklist_classes,
        ],
      ],
    ])->save();
    $page = [];
    $this->container
      ->get('main_content_renderer.html')
      ->invokePageAttachmentHooks($page);
    self::assertContains('google_tag/gtm', $page['#attached']['library']);
    self::assertEquals([
      'tagIds' => ['GTM-XXXXXX', 'GTM-YYYYYY'],
      'settings' => [
        'include_classes' => TRUE,
        'allowlist_classes' => explode(PHP_EOL, $allowlist_classes),
        'blocklist_classes' => explode(PHP_EOL, $blocklist_classes),
      ],
    ],
    $page['#attached']['drupalSettings']['gtm']);
  }

}
