<?php

namespace Drupal\Tests\advagg_validator\Functional;

use Drupal\Tests\advagg\Functional\AdminPagesTest;

/**
 * Tests that all the AdvAgg validator path(s) return valid content.
 *
 * @ingroup advagg_tests
 *
 * @group advagg
 */
class ValidatorPagesTest extends AdminPagesTest {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['advagg_validator'];

  /**
   * Routes to test.
   *
   * @var array
   */
  public $routes = [];
  // Should be ['advagg_validator.jshint',
  // 'advagg_validator.csslint',
  // 'advagg_validator.cssw3']
  // Disabling temporarily due to memory issues while testing.
  // memory usage on validation pages should be fixed.
}
