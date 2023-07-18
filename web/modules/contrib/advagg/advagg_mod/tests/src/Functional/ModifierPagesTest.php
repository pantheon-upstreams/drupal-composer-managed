<?php

namespace Drupal\Tests\advagg_mod\Functional;

use Drupal\Tests\advagg\Functional\AdminPagesTest;

/**
 * Tests that all the AdvAgg Modifier path(s) return valid content.
 *
 * @ingroup advagg_tests
 *
 * @group advagg
 */
class ModifierPagesTest extends AdminPagesTest {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['advagg_mod'];

  /**
   * Routes to test.
   *
   * @var array
   */
  public $routes = ['advagg_mod.settings'];

}
