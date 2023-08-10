<?php

namespace Drupal\Tests\advagg_cdn\Functional;

use Drupal\Core\Url;
use Drupal\Tests\advagg\Functional\AdvaggFunctionalTestBase;

/**
 * Tests that AdvAgg CDN changes are properly applied.
 *
 * @ingroup advagg_tests
 *
 * @group advagg
 */
class CdnTest extends AdvaggFunctionalTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['advagg_cdn', 'advagg_test'];

  /**
   * Test the cdn functionality and settings configuration.
   */
  public function testCdn() {
    // Load config form and ensure jQuery js is being modified.
    $this->drupalGet(Url::fromRoute('advagg_cdn.settings'));
    $session = $this->assertSession();
    $session->statusCodeEquals(200);
    $session->responseContains('src="//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"');
    $session->responseNotContains('jquery-ui.min.css');

    // Test config form function, enable cdn for jQueryUI, disable minification.
    $edit = [
      'jquery_ui_css' => TRUE,
      'minified' => FALSE,
    ];

    $this->submitForm($edit, $this->t('Save configuration'));
    $session = $this->assertSession();
    $session->responseNotContains('jquery.min.js');
    $this->assertTrue($this->config('advagg_cdn.settings')->get('jquery_ui_css'));
    $session->responseContains('jquery.js');
    $session->responseContains('jquery-ui.css');
  }

}
