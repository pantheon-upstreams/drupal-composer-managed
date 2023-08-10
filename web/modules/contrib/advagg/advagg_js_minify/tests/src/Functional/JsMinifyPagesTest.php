<?php

namespace Drupal\Tests\advagg_js_minify\Functional;

use Drupal\Tests\advagg\Functional\AdminPagesTest;

/**
 * Tests that all the AdvAgg JS Minifier path(s) return valid content.
 *
 * @ingroup advagg_tests
 *
 * @group advagg
 */
class JsMinifyPagesTest extends AdminPagesTest {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['advagg_js_minify'];

  /**
   * Routes to test.
   *
   * @var array
   */
  public $routes = ['advagg_js_minify.settings'];

}
