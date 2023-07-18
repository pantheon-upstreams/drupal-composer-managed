<?php

namespace Drupal\Tests\advagg_bundler\Functional;

use Drupal\Core\Url;
use Drupal\Tests\advagg\Functional\AdvaggFunctionalTestBase;

/**
 * Tests that AdvAgg Bundler is functioning correctly.
 *
 * @ingroup advagg_tests
 *
 * @group advagg
 */
class BundlerTest extends AdvaggFunctionalTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['advagg_bundler', 'advagg_bundler_test'];

  /**
   * Test the bundler functionality and settings configuration.
   */
  public function testBundler() {
    // Agreggrate files.
    $this->config('system.performance')
      ->set('css.preprocess', TRUE)
      ->set('js.preprocess', TRUE)
      ->save();

    // Load config form and ensure jQuery js is being modified.
    $this->drupalGet(Url::fromRoute('advagg_bundler.settings'));
    $session = $this->assertSession();
    $session->statusCodeEquals(200);

    // Requires: https://github.com/minkphp/Mink/pull/744
    // $session->responseContainsCount("<script", 8);
    // $session->responseContainsCount('rel="stylesheet"', 4);
    //
    // Test config form function, enable cdn for jQueryUI, disable minification.
    $edit = [
      'active' => TRUE,
      'max_css' => 6,
      'css_logic' => 0,
      'max_js' => 9,
      'js_logic' => 0,
    ];
    $this->submitForm( $edit, $this->t('Save configuration'));
    $session = $this->assertSession();
    $session->statusCodeEquals(200);

    // The Drupal settings json is a script element but inline so
    // AdvAgg ignores it.
    // Requires: https://github.com/minkphp/Mink/pull/744
    // $session->responseContainsCount("<script", 11);
    // $session->responseContainsCount('rel="stylesheet"', 6);
    //
    // Ensure that bundler works even if the number of non-preprocessed files
    // *exactly* match the selected maximum. See advagg_bundler_test.module.
    $edit['max_js'] = 1;
    $edit['max_css'] = 1;
    $this->submitForm( $edit, $this->t('Save configuration'));

    $this->drupalGet(Url::fromRoute('system.admin'));
    $session = $this->assertSession();
    $session->statusCodeEquals(200);
  }

}
