<?php

namespace Drupal\Tests\hierarchical_taxonomy_menu\Functional;

use Drupal\Tests\block\Traits\BlockCreationTrait;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests the Hierarchical Taxonomy Menu.
 *
 * @group hierarchical_taxonomy_menu
 */
class HierarchicalTaxonomyMenuTest extends BrowserTestBase {

  use BlockCreationTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'block',
    'image',
    'hierarchical_taxonomy_menu',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $admin_user = $this->drupalCreateUser([
      'administer blocks',
      'administer site configuration',
      'access administration pages',
    ]);

    $this->drupalLogin($admin_user);
  }

  /**
   * Test that the block is available.
   */
  public function testBlockAvailability() {
    $this->drupalGet('/admin/structure/block');
    $this->clickLink('Place block');
    $this->assertSession()->pageTextContains('Hierarchical Taxonomy Menu');
    $this->assertSession()->linkByHrefExists('admin/structure/block/add/hierarchical_taxonomy_menu/', 0);
  }

  /**
   * Test that the block can be placed.
   */
  public function testBlockPlacement() {
    $this->drupalPlaceBlock('hierarchical_taxonomy_menu', [
      'region' => 'content',
      'label' => 'Hierarchical Taxonomy Menu',
      'id' => 'hierarchicaltaxonomymenu',
    ]);

    $this->drupalGet('admin/structure/block');
    $this->assertSession()->pageTextContains('Hierarchical Taxonomy Menu');

    $this->drupalGet('<front>');
    $this->assertSession()->pageTextContains('Hierarchical Taxonomy Menu');
  }

  /**
   * Test the block config form integrity.
   */
  public function testBlockConfigForm() {
    $this->drupalPlaceBlock('hierarchical_taxonomy_menu', [
      'region' => 'content',
      'label' => 'Hierarchical Taxonomy Menu',
      'id' => 'hierarchicaltaxonomymenu',
    ]);

    $this->drupalGet('admin/structure/block/manage/hierarchicaltaxonomymenu');
    $this->assertSession()->statusCodeEquals(200);

    $this->assertSession()->fieldExists('settings[basic][vocabulary]');
    $this->assertSession()->fieldExists('settings[basic][max_depth]');
    $this->assertSession()->optionExists('edit-settings-basic-max-depth', '0');
    $this->assertSession()->optionExists('edit-settings-basic-max-depth', '1');
    $this->assertSession()->optionExists('edit-settings-basic-max-depth', '2');
    $this->assertSession()->optionExists('edit-settings-basic-max-depth', '3');
    $this->assertSession()->optionExists('edit-settings-basic-max-depth', '4');
    $this->assertSession()->optionExists('edit-settings-basic-max-depth', '5');
    $this->assertSession()->optionExists('edit-settings-basic-max-depth', '6');
    $this->assertSession()->optionExists('edit-settings-basic-max-depth', '7');
    $this->assertSession()->optionExists('edit-settings-basic-max-depth', '8');
    $this->assertSession()->optionExists('edit-settings-basic-max-depth', '9');
    $this->assertSession()->optionExists('edit-settings-basic-max-depth', '10');
    $this->assertSession()->optionExists('edit-settings-basic-max-depth', '100');
    $this->assertSession()->fieldExists('settings[basic][dynamic_block_title]');
    $this->assertSession()->fieldExists('settings[basic][collapsible]');
    $this->assertSession()->fieldExists('settings[basic][stay_open]');
    $this->assertSession()->fieldExists('settings[basic][interactive_parent]');
    $this->assertSession()->fieldExists('settings[basic][hide_block]');

    $this->assertSession()->fieldExists('settings[image][use_image_style]');
    $this->assertSession()->fieldExists('settings[image][image_style]');
    $this->assertSession()->fieldExists('settings[image][image_height]');
    $this->assertSession()->fieldExists('settings[image][image_width]');
    $this->assertSession()->optionExists('edit-settings-image-image-style', 'large');
    $this->assertSession()->optionExists('edit-settings-image-image-style', 'medium');
    $this->assertSession()->optionExists('edit-settings-image-image-style', 'thumbnail');

    $this->assertSession()->fieldExists('settings[advanced][max_age]');
    $this->assertSession()->optionExists('edit-settings-advanced-max-age', '0');
    $this->assertSession()->optionExists('edit-settings-advanced-max-age', '1800');
    $this->assertSession()->optionExists('edit-settings-advanced-max-age', '3600');
    $this->assertSession()->optionExists('edit-settings-advanced-max-age', '21600');
    $this->assertSession()->optionExists('edit-settings-advanced-max-age', '43200');
    $this->assertSession()->optionExists('edit-settings-advanced-max-age', '86400');
    $this->assertSession()->optionExists('edit-settings-advanced-max-age', '604800');
    $this->assertSession()->optionExists('edit-settings-advanced-max-age', 'PERMANENT');
    $this->assertSession()->fieldExists('settings[advanced][base_term]');
    $this->assertSession()->fieldExists('settings[advanced][dynamic_base_term]');
    $this->assertSession()->fieldExists('settings[advanced][show_count]');
    $this->assertSession()->fieldExists('settings[advanced][calculate_count_recursively]');
  }

}
