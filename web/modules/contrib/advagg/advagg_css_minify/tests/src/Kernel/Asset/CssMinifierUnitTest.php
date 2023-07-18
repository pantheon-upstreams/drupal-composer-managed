<?php

namespace Drupal\Tests\advagg_css_minify\Kernel\Asset;

use Drupal\KernelTests\KernelTestBase;

/**
 * Tests the CSS asset minifier.
 *
 * @group advagg_css_minify
 * @group advagg
 */
class CssMinifierUnitTest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['advagg_css_minify'];

  /**
   * The Minifier.
   *
   * @var \Drupal\advagg_css_minify\Asset\CssMinifier
   */
  protected $optimizer;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installConfig('advagg_css_minify');
    $this->optimizer = \Drupal::service('advagg.css_minifier');
  }

  /**
   * Tests running the minifier with the minifier disabled.
   */
  public function testNoMinifier() {
    $this->config('advagg_css_minify.settings')->set('minifier', 0)->save();
    $file = dirname(__FILE__) . '/css_test_files/css_input_without_import.css';
    $contents = file_get_contents($file);
    $this->assertSame($contents, $this->optimizer->optimize($contents, [], []));
  }

  /**
   * Provides data for the CSS asset optimizing test.
   */
  public function providerTestMinifyYui() {
    $path = 'core/tests/Drupal/Tests/Core/Asset/css_test_files/';
    $absolute_path = dirname(__FILE__) . '/css_test_files/';
    return [
      // File. Tests:
      // - Stripped comments and white-space.
      // - Retain white-space in selectors. (https://www.drupal.org/node/472820)
      // - Retain pseudo-selectors. (https://www.drupal.org/node/460448)
      [
        [
          'data' => $path . 'css_input_without_import.css',
        ],
        file_get_contents($absolute_path . 'css_input_without_import.css'),
        file_get_contents($absolute_path . 'css_input_without_import.css.optimized.css'),
      ],
      // File. Tests:
      // - Retain comment hacks.
      [
        [
          'data' => $path . 'comment_hacks.css',
        ],
        file_get_contents($absolute_path . 'comment_hacks.css'),
        file_get_contents($absolute_path . 'comment_hacks.css.optimized.css'),
      ],
      // File. Tests:
      // - Any @charset declaration at the beginning of a file should be
      //   removed without breaking subsequent CSS.
      [
        [
          'data' => $path . 'charset_sameline.css',
        ],
        file_get_contents($absolute_path . 'charset_sameline.css'),
        file_get_contents($absolute_path . 'charset.css.optimized.css'),
      ],
      [
        [
          'data' => $path . 'charset_newline.css',
        ],
        file_get_contents($absolute_path . 'charset_newline.css'),
        file_get_contents($absolute_path . 'charset.css.optimized.css'),
      ],
      [
        [
          'data' => $path . 'css_input_with_bom.css',
        ],
        file_get_contents($absolute_path . 'css_input_with_bom.css'),
        '.byte-order-mark-test{content:"☃"}',
      ],
      [
        [
          'data' => $path . 'css_input_with_charset.css',
        ],
        file_get_contents($absolute_path . 'css_input_with_charset.css'),
        '.charset-test{content:"€"}',
      ],
      [
        [
          'data' => $path . 'css_input_with_bom_and_charset.css',
        ],
        file_get_contents($absolute_path . 'css_input_with_bom_and_charset.css'),
        '.byte-order-mark-charset-test{content:"☃"}',
      ],
      [
        [
          'data' => $path . 'css_input_with_utf16_bom.css',
        ],
        file_get_contents($absolute_path . 'css_input_with_utf16_bom.css'),
        '.utf16-byte-order-mark-test{content:"☃"}',
      ],
      [
        [
          'data' => $path . 'quotes.css',
        ],
        file_get_contents($absolute_path . 'quotes.css'),
        file_get_contents($absolute_path . 'quotes.css.optimized.css'),
      ],
    ];
  }

  /**
   * Tests minification of a CSS Asset with YUI.
   *
   * @param array $css_asset
   *   A fake asset array with the filepath to pass to the minifier.
   * @param string $original
   *   The asset string contents to be minified.
   * @param string $expected
   *   The expected result of minification.
   *
   * @dataProvider providerTestMinifyYui
   */
  public function testMinifyYui(array $css_asset, $original, $expected) {
    $this->assertEquals($expected, $this->optimizer->optimize($original, $css_asset, []));
  }

}
