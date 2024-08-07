<?php

namespace Drupal\Tests\advagg\Functional;

use Drupal\Core\Url;

/**
 * Tests that all the admin path(s) return valid content.
 *
 * @ingroup advagg_tests
 *
 * @group advagg
 */
class AdminPagesTest extends AdvaggFunctionalTestBase {

  /**
   * Routes to test.
   *
   * @var array
   */
  public $routes = ['advagg.settings', 'advagg.info', 'advagg.operations'];

  /**
   * Tests that the main admin path returns correct contents.
   */
  public function testLoad() {
    foreach ($this->routes as $route) {
      $this->drupalGet(Url::fromRoute($route));
      $this->assertSession()->statusCodeEquals(200);
    }
  }

}
