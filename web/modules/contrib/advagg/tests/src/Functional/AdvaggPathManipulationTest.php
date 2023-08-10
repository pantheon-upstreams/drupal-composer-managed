<?php

namespace Drupal\Tests\advagg\Functional;

use Drupal\Core\Url;

/**
 * Tests that all the asset path settings function correctly.
 *
 * @ingroup advagg_tests
 *
 * @group advagg
 */
class AdvaggPathManipulationTest extends AdvaggFunctionalTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['advagg', 'advagg_test'];

  /**
   * Tests path converting functions, and that saving a change to them works.
   */
  public function testPathFunctions() {
    global $base_path;
    // Test convert absolute to protocol relative paths.
    $this->drupalGet(Url::fromRoute('advagg.settings'));
    $session = $this->assertSession();
    $session->responseContains('src="//cdn.jsdelivr.net/jquery.actual/1.0.18/jquery.actual.min.js');
    $session->responseContains('src="' . $base_path . 'core/assets/vendor/modernizr/modernizr.min.js');

    // Test convert force HTTPS.
    $edit = [];
    $edit['path_convert_absolute_to_protocol_relative'] = FALSE;
    $edit['path_convert_force_https'] = TRUE;
    $edit['path_convert_absolute'] = FALSE;
    $this->submitForm($edit, 'op');

    $config = $this->config('advagg.settings');
    $this->assertTrue($config->get('path.convert.force_https'));
    $session = $this->assertSession();
    $session->responseContains('src="https://cdn.jsdelivr.net/jquery.actual/1.0.18/jquery.actual.min.js');
    $session->responseContains('src="' . $base_path . 'core/assets/vendor/modernizr/modernizr.min.js');

    // Test convert force absolute path.
    $edit = [];
    $edit['path_convert_absolute_to_protocol_relative'] = FALSE;
    $edit['path_convert_force_https'] = FALSE;
    $edit['path_convert_absolute'] = TRUE;
    $this->submitForm($edit, 'op');

    global $base_root;
    $config = $this->config('advagg.settings');
    $this->assertTrue($config->get('path.convert.absolute'));
    $session = $this->assertSession();
    $session->responseContains('src="http://cdn.jsdelivr.net/jquery.actual/1.0.18/jquery.actual.min.js');
    $session->responseContains('src="' . $base_root . $base_path . 'core/assets/vendor/modernizr/modernizr.min.js');
  }

}
