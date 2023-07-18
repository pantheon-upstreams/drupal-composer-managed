<?php

namespace Drupal\Tests\advagg_js_minify\Kernel\Asset;

use Drupal\KernelTests\KernelTestBase;

/**
 * Tests the JS asset minifier.
 *
 * @group advagg_js_minify
 * @group advagg
 */
class JsMinifierUnitTest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['advagg_js_minify'];

  /**
   * The Minifier.
   *
   * @var \Drupal\advagg_js_minify\Asset\JsMinifier
   */
  protected $optimizer;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installConfig('advagg_js_minify');
    $this->optimizer = \Drupal::service('advagg.js_minifier');
  }

  /**
   * Tests running the minifier with the minifier disabled.
   */
  public function testNoMinifier() {
    $file = dirname(__FILE__) . '/js_test_files/drupal.js';
    $contents = file_get_contents($file);
    $this->assertSame($contents, $this->optimizer->optimize($contents, [], []));
  }

  /**
   * Provides data for the JS asset cleaning test.
   *
   * @see \Drupal\Core\Asset\JsOptimizer::clean()
   *
   * @returns array
   *   An array of test data.
   */
  public function providerTestClean() {
    $dir = dirname(__FILE__) . '/js_test_files/';
    return [
      // File. Tests:
      // - Stripped sourceMappingURL with comment # syntax.
      0 => [
        file_get_contents($dir . 'source_mapping_url.min.js'),
        file_get_contents($dir . 'source_mapping_url.min.js.optimized.js'),
      ],
      // File. Tests:
      // - Stripped sourceMappingURL with comment @ syntax.
      1 => [
        file_get_contents($dir . 'source_mapping_url_old.min.js'),
        file_get_contents($dir . 'source_mapping_url_old.min.js.optimized.js'),
      ],
      // File. Tests:
      // - Stripped sourceURL with comment # syntax.
      2 => [
        file_get_contents($dir . 'source_url.min.js'),
        file_get_contents($dir . 'source_url.min.js.optimized.js'),
      ],
      // File. Tests:
      // - Stripped sourceURL with comment @ syntax.
      3 => [
        file_get_contents($dir . 'source_url_old.min.js'),
        file_get_contents($dir . 'source_url_old.min.js.optimized.js'),
      ],
    ];
  }

  /**
   * Tests cleaning of a JavaScript asset.
   *
   * @param string $original
   *   The unprocessed asset string.
   * @param string $expected
   *   The expected cleaned asset string.
   *
   * @dataProvider providerTestClean
   */
  public function testClean($original, $expected) {
    $this->assertEquals($expected, $this->optimizer->clean($original, []));
  }

  /**
   * Provides data for the JS asset optimize test.
   *
   * @see \Drupal\Core\Asset\JsOptimizer::optimize()
   *
   * @returns array
   *   An array of test data.
   */
  public function providerTestMinification() {
    $path = dirname(__FILE__) . '/js_test_files/';
    return [
      [
        [
          'data' => $path . 'drupal.js',
        ],
        file_get_contents($path . 'drupal.js'),
      ],
      [
        [
          'data' => $path . 'ajax.js',
        ],
        file_get_contents($path . 'ajax.js'),
      ],
      [
        [
          'data' => $path . 'ToolbarVisualView.js',
        ],
        file_get_contents($path . 'ToolbarVisualView.js'),
      ],
    ];
  }

  /**
   * Tests minification of a JS Asset with jsmin.
   *
   * @param array $js_asset
   *   An fake asset array with the filepath to pass to the minifier.
   * @param string $contents
   *   The asset string contents to be minified.
   *
   * @dataProvider providerTestMinification
   */
  public function testMinifyJsmin(array $js_asset, $contents) {
    // Requires the JSMin PHP extension; if it isn't available skip the test.
    if (!function_exists('jsmin')) {
      $this->markTestSkipped('The function jsmin doesn\'t exist, requires the JSMin PHP extension.');
    }
    $this->config('advagg_js_minify.settings')->set('minifier', 3)->save();
    $expected = file_get_contents($js_asset['data'] . '.jsmin.js');
    $this->assertEquals($expected, $this->optimizer->optimize($contents, $js_asset, []));
  }

  /**
   * Tests minification of a JS Asset with JSMinplus.
   *
   * @param array $js_asset
   *   An fake asset array with the filepath to pass to the minifier.
   * @param string $contents
   *   The asset string contents to be minified.
   *
   * @dataProvider providerTestMinification
   */
  public function testMinifyJsminPlus(array $js_asset, $contents) {
    $this->config('advagg_js_minify.settings')->set('minifier', 1)->save();
    $expected = file_get_contents($js_asset['data'] . '.jsminplus.js');
    $this->assertEquals($expected, $this->optimizer->optimize($contents, $js_asset, []));
  }

  /**
   * Tests minification of a JS Asset with JSqueeze.
   *
   * @param array $js_asset
   *   An fake asset array with the filepath to pass to the minifier.
   * @param string $contents
   *   The asset string contents to be minified.
   *
   * @dataProvider providerTestMinification
   */
  public function testMinifyJsqueeze(array $js_asset, $contents) {
    $this->config('advagg_js_minify.settings')->set('minifier', 5)->save();

    // Due to an odd bug we use a different test file for PHP5.x comparison.
    // See https://www.drupal.org/node/2916193.
    if (version_compare(phpversion(), '7', '<')) {
      $js_asset['data'] .= '.php5';
    }
    $expected = file_get_contents($js_asset['data'] . '.jsqueeze.js');
    $this->assertEquals($expected, $this->optimizer->optimize($contents, $js_asset, []));
  }

  /**
   * Tests minification of a JS Asset with JShrink.
   *
   * @param array $js_asset
   *   An fake asset array with the filepath to pass to the minifier.
   * @param string $contents
   *   The asset string contents to be minified.
   *
   * @dataProvider providerTestMinification
   */
  public function testMinifyJshrink(array $js_asset, $contents) {
    $this->config('advagg_js_minify.settings')->set('minifier', 4)->save();
    $expected = file_get_contents($js_asset['data'] . '.jshrink.js');
    $this->assertEquals($expected, $this->optimizer->optimize($contents, $js_asset, []));
  }

  /**
   * Tests JSqueeze minification's important comments/integration.
   */
  public function testMinifyJsqueezeImportantComments() {
    $file = dirname(__FILE__) . '/js_test_files/jquery.once.js';
    $asset = ['data' => $file];
    $original = file_get_contents($file);
    $withComments = file_get_contents($file . '.with-important.js');
    $withoutComments = file_get_contents($file . '.without-important.js');
    $config = $this->config('advagg_js_minify.settings');
    $config
      ->set('minifier', 5)
      ->set('add_license', TRUE)
      ->save();
    $this->assertEquals($withComments, $this->optimizer->optimize($original, $asset, []));

    $config->set('add_license', FALSE)->save();
    $this->assertEquals($withoutComments, $this->optimizer->optimize($original, $asset, []));
  }

}
