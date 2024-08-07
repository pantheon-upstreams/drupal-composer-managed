<?php

namespace Drupal\Tests\advagg\functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * @defgroup advagg_tests Test Suit
 *
 * @{
 * The automated test suit for Advanced Aggregates.
 *
 * @}
 */

/**
 * Base test class for Advagg test cases.
 */
abstract class AdvaggFunctionalTestBase extends BrowserTestBase {

  use StringTranslationTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['advagg'];

  /**
   * A user with permission to administer site configuration.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $user;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->user = $this->drupalCreateUser([
      'administer site configuration',
      'access administration pages',
    ]);
    $this->drupalLogin($this->user);
  }

}
