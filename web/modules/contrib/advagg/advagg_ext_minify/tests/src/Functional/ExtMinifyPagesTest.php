<?php

namespace Drupal\Tests\advagg_ext_minify\Functional;

use Drupal\Tests\advagg\Functional\AdminPagesTest;

/**
 * Tests that all the AdvAgg External Minification path(s) return valid content.
 *
 * @ingroup advagg_tests
 *
 * @group advagg
 */
class ExtMinifyPagesTest extends AdminPagesTest {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['advagg_ext_minify'];

  /**
   * Routes to test.
   *
   * @var array
   */
  public $routes = ['advagg_ext_minify.settings'];

}
