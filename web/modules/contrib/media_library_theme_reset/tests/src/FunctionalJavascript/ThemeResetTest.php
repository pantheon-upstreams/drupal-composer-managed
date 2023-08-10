<?php

namespace Drupal\Tests\media_library_theme_reset\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\media\Entity\Media;

/**
 * Tests that the module styles are appended to media library.
 *
 * @group media_library_theme_reset
 */
class ThemeResetTest extends WebDriverTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'node',
    'field',
    'field_ui',
    'layout_discovery',
    'layout_builder',
    'contextual',
    'media_library',
    'media',
    'views',
    'file',
    'image',
    'text',
    'filter',
    'user',
    'block',
    'block_content',
    'media_library_theme_reset',
    'media_library_theme_reset_test_content_type',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $account = $this->drupalCreateUser([
      'administer content types',
      'administer node display',
      'create media',
      'view media',
      'configure any layout',
      'create and edit custom blocks',
      'access content',
    ]);
    $this->drupalLogin($account);
  }

  /**
   * Test styles inherited from module.
   */
  public function testCustomBlockStyles(): void {
    $session = $this->getSession();
    $page = $session->getPage();
    $assert = $this->assertSession();
    
    $session->resizeWindow(1200, 2000);
    // Visit the content layout manage page.
    $this->drupalGet('admin/structure/types/manage/basic_page/display/default/layout');
    // Try adding a new block with Layout Builder.
    $page->clickLink('Add block');
    $assert->waitForText('Create custom block');
    // Start creating a custom block.
    $page->clickLink('Create custom block');
    $assert->waitForText('Add media');
    // Open the media library by clicking the add media button.
    $page->pressButton('Add media');
    $assert->waitForText('Add or select media');
    // Assert submit button has color provided by module styles.
    $actual_background = $session->evaluateScript('jQuery(".ui-dialog-buttonpane .ui-dialog-buttonset .media-library-select").css("background-color")');
    $this->assertSame("rgb(0, 113, 184)", $actual_background);
  }

}
