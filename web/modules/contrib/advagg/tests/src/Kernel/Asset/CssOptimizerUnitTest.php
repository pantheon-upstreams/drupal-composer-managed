<?php

namespace Drupal\Tests\advagg\Kernel\Asset;

use Drupal\KernelTests\KernelTestBase;

/**
 * Tests the CSS asset optimizer.
 *
 * @group advagg
 */
class CssOptimizerUnitTest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['advagg'];

  /**
   * The Optimizer.
   *
   * @var \Drupal\advagg\Asset\CssOptimizer
   */
  protected $optimizer;

  /**
   * The directory for comparison/getting CSS files from.
   *
   * @var string
   */
  protected $dir;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installConfig('advagg');
    $this->optimizer = \Drupal::service('advagg.optimizer.css');
    $this->dir = base_path() . 'modules/contrib/advagg/tests/src/Kernel/Asset/';
  }

  /**
   * Provides data for the url update test.
   */
  public function providerTestUrlUpdate() {
    return [
      [
        'url_test_same_dir.css',
        $this->dir . 'css_test_files/icon-foo.svg',
      ],
      [
        'url_test_child_dir.css',
        $this->dir . 'css_test_files/images/icon-foo.svg',
      ],
      [
        'url_test_parent_dir.css',
        $this->dir . 'css_test_files/../images/icon-foo.svg',
      ],
    ];
  }

  /**
   * Tests the urlUpdate() method.
   *
   * @param string $file
   *   The file to test.
   * @param string $expected
   *   The expected url string.
   *
   * @dataProvider providerTestUrlUpdate
   */
  public function testUrlUpdate($file, $expected) {
    $path = $this->dir . 'css_test_files/' . $file;
    $absolute_path = dirname(__FIlE__) . '/css_test_files/';
    $contents = file_get_contents($absolute_path . $file);
    $replaced_urls = $this->optimizer->updateUrls($contents, $path);
    $this->assertNotFalse(strstr($replaced_urls, $expected));
  }

}
