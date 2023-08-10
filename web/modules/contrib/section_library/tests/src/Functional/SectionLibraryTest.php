<?php

namespace Drupal\Tests\section_library\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Test install of module.
 *
 * @group section_library
 */
class SectionLibraryTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stable';

  /**
   * An admin user with permission to use the Section Library.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;


  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'section_library',
    'layout_builder',
    'options',
    'file',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->adminUser = $this->drupalCreateUser([
      'administer site configuration',
      'access administration pages',
      'import template from section library',
      'view section library templates',
      'add section library templates',
      'edit section library templates',
      'delete section library templates',
      'administer section library template entities',
    ]);

  }

  /**
   * Test callback.
   */
  public function testInstall(): void {
    $this->drupalLogin($this->adminUser);
    $this->drupalGet('/admin/content/section-library');
    $this->assertSession()->pageTextContains('There are no section library template entities yet.');
  }

}
