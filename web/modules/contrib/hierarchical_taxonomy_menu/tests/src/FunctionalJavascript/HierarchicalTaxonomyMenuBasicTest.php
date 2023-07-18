<?php

namespace Drupal\Tests\hierarchical_taxonomy_menu\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\taxonomy\Entity\Term;
use Drupal\Tests\block\Traits\BlockCreationTrait;
use Drupal\Tests\taxonomy\Traits\TaxonomyTestTrait;

/**
 * Tests the Hierarchical Taxonomy Menu basic config options.
 *
 * @group hierarchical_taxonomy_menu
 */
class HierarchicalTaxonomyMenuBasicTest extends WebDriverTestBase {

  use BlockCreationTrait;
  use TaxonomyTestTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'block',
    'image',
    'hierarchical_taxonomy_menu',
    'taxonomy',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * The vocabulary.
   *
   * @var \Drupal\taxonomy\VocabularyInterface
   */
  protected $vocabulary;

  /**
   * The parent term ID.
   *
   * @var int
   */
  protected $childParent;

  /**
   * The placed Hierarchical Taxonomy Menu block.
   *
   * @var \Drupal\block\Entity\Block
   */
  protected $block;

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

    $this->vocabulary = $this->createVocabulary();

    // Generate taxonomy tree with 3 parents. Each parent has 11 descendants.
    // @codingStandardsIgnoreStart
    //  Parent term 1 (Term ID: 1)
    //    Child term 1-1 (Term ID: 2)
    //      Child term 1-2 (Term ID: 3)
    //        Child term 1-3 (Term ID: 4)
    //          Child term 1-4 (Term ID: 5)
    //            Child term 1-5 (Term ID: 6)
    //              Child term 1-6 (Term ID: 7)
    //                Child term 1-7 (Term ID: 8)
    //                  Child term 1-8 (Term ID: 9)
    //                    Child term 1-9 (Term ID: 10)
    //                      Child term 1-10 (Term ID: 11)
    //                        Child term 1-11 (Term ID: 12)
    //  Parent term 2 (Term ID: 13)
    //    Child term 2-1 (Term ID: 14)
    //      Child term 2-2 (Term ID: 15)
    //        Child term 2-3 (Term ID: 16)
    //          Child term 2-4 (Term ID: 17)
    //            Child term 2-5 (Term ID: 18)
    //              Child term 2-6 (Term ID: 19)
    //                Child term 2-7 (Term ID: 20)
    //                  Child term 2-8 (Term ID: 21)
    //                    Child term 2-9 (Term ID: 22)
    //                      Child term 2-10 (Term ID: 23)
    //                        Child term 2-11 (Term ID: 24)
    //  Parent term 3 (Term ID: 25)
    //    Child term 3-1 (Term ID: 26)
    //      Child term 3-2 (Term ID: 27)
    //        Child term 3-3 (Term ID: 28)
    //          Child term 3-4 (Term ID: 29)
    //            Child term 3-5 (Term ID: 30)
    //              Child term 3-6 (Term ID: 31)
    //                Child term 3-7 (Term ID: 32)
    //                  Child term 3-8 (Term ID: 33)
    //                    Child term 3-9 (Term ID: 34)
    //                      Child term 3-10 (Term ID: 35)
    //                        Child term 3-11 (Term ID: 36)
    // @codingStandardsIgnoreEnd

    for ($i = 1; $i < 4; $i++) {
      $parent = Term::create([
        'vid' => $this->vocabulary->id(),
        'name' => 'Parent term ' . $i,
      ]);
      $parent->save();

      for ($n = 1; $n < 12; $n++) {
        $child = Term::create([
          'vid' => $this->vocabulary->id(),
          'name' => 'Child term ' . $i . '-' . $n,
        ]);

        if ($n == 1) {
          $child->parent = $parent->id();
        }
        else {
          $child->parent = $this->childParent;
        }

        $child->save();
        $this->childParent = $child->id();
      }
    }

    $this->block = $this->drupalPlaceBlock('hierarchical_taxonomy_menu', [
      'region' => 'content',
      'label' => 'Hierarchical Taxonomy Menu',
      'id' => 'hierarchicaltaxonomymenu',
    ]);
  }

  /**
   * Test zero depth.
   */
  public function testZeroDepth() {
    $this->block->set('settings', [
      'label' => 'Hierarchical Taxonomy Menu',
      'label_display' => 'visible',
      'vocabulary' => $this->vocabulary->id() . '|',
      'max_depth' => 0,
    ]);
    $this->block->save();

    $this->drupalGet('<front>');

    // We should see just parents. All descendants must be hidden.
    for ($i = 1; $i < 4; $i++) {
      $this->assertSession()->pageTextContains('Parent term ' . $i);

      for ($n = 1; $n < 12; $n++) {
        $this->assertSession()->pageTextNotContains('Child term ' . $i . '-' . $n);
      }
    }
  }

  /**
   * Test unlimited depth.
   */
  public function testUnlimitedDepth() {
    $this->block->set('settings', [
      'label' => 'Hierarchical Taxonomy Menu',
      'label_display' => 'visible',
      'vocabulary' => $this->vocabulary->id() . '|',
      'max_depth' => 100,
    ]);
    $this->block->save();

    $this->drupalGet('<front>');

    // We should see parents and all descendants.
    for ($i = 1; $i < 4; $i++) {
      $this->assertSession()->pageTextContains('Parent term ' . $i);

      for ($n = 1; $n < 12; $n++) {
        $this->assertSession()->pageTextContains('Child term ' . $i . '-' . $n);
      }
    }
  }

  /**
   * Test dynamic block title enabled.
   */
  public function testDynamicBlockTitleEnabled() {
    $this->block->set('settings', [
      'label' => 'Hierarchical Taxonomy Menu',
      'label_display' => 'visible',
      'vocabulary' => $this->vocabulary->id() . '|',
      'dynamic_block_title' => TRUE,
    ]);
    $this->block->save();

    $this->drupalGet('taxonomy/term/1');
    $block_title_element = $this->getSession()->getPage()->find('css', '#block-hierarchicaltaxonomymenu h2');
    $this->assertEqual($block_title_element->getText(), 'Parent term 1');
  }

  /**
   * Test dynamic block title disabled.
   */
  public function testDynamicBlockTitleDisabled() {
    $this->block->set('settings', [
      'label' => 'Hierarchical Taxonomy Menu',
      'label_display' => 'visible',
      'vocabulary' => $this->vocabulary->id() . '|',
      'dynamic_block_title' => FALSE,
    ]);
    $this->block->save();

    $this->drupalGet('taxonomy/term/1');
    $block_title_element = $this->getSession()->getPage()->find('css', '#block-hierarchicaltaxonomymenu h2');
    $this->assertEqual($block_title_element->getText(), 'Hierarchical Taxonomy Menu');
  }

  /**
   * Test the block with collapsed content.
   */
  public function testCollapsed() {
    $this->block->set('settings', [
      'label' => 'Hierarchical Taxonomy Menu',
      'label_display' => 'visible',
      'vocabulary' => $this->vocabulary->id() . '|',
      'collapsible' => TRUE,
    ]);
    $this->block->save();

    $this->drupalGet('<front>');

    // We should see just parents. All descendants must be hidden.
    for ($i = 1; $i < 4; $i++) {
      $this->assertSession()->pageTextContains('Parent term ' . $i);

      for ($n = 1; $n < 12; $n++) {
        $this->assertSession()->pageTextNotContains('Child term ' . $i . '-' . $n);
      }
    }
  }

  /**
   * Test the block with not collapsed content.
   */
  public function testNotCollapsed() {
    $this->block->set('settings', [
      'label' => 'Hierarchical Taxonomy Menu',
      'label_display' => 'visible',
      'vocabulary' => $this->vocabulary->id() . '|',
      'collapsible' => FALSE,
    ]);
    $this->block->save();

    $this->drupalGet('<front>');

    // We should see parents and all descendants.
    for ($i = 1; $i < 4; $i++) {
      $this->assertSession()->pageTextContains('Parent term ' . $i);

      for ($n = 1; $n < 12; $n++) {
        $this->assertSession()->pageTextContains('Child term ' . $i . '-' . $n);
      }
    }
  }

  /**
   * Test the block with collapsed and stay open content.
   */
  public function testCollapsedAndStayOpen() {
    $this->block->set('settings', [
      'label' => 'Hierarchical Taxonomy Menu',
      'label_display' => 'visible',
      'vocabulary' => $this->vocabulary->id() . '|',
      'collapsible' => TRUE,
      'stay_open' => TRUE,
    ]);
    $this->block->save();

    $this->drupalGet('taxonomy/term/12');

    // We should see all parents and all descendants of the first parent.
    for ($i = 1; $i < 4; $i++) {
      $this->assertSession()->pageTextContains('Parent term ' . $i);

      for ($n = 1; $n < 12; $n++) {
        if ($i == 1) {
          $this->assertSession()->pageTextContains('Child term ' . $i . '-' . $n);
        }
        else {
          $this->assertSession()->pageTextNotContains('Child term ' . $i . '-' . $n);
        }
      }
    }
  }

  /**
   * Test the block with collapsed and stay closed content.
   */
  public function testCollapsedAndStayClosed() {
    $this->block->set('settings', [
      'label' => 'Hierarchical Taxonomy Menu',
      'label_display' => 'visible',
      'vocabulary' => $this->vocabulary->id() . '|',
      'collapsible' => TRUE,
      'stay_open' => FALSE,
    ]);
    $this->block->save();

    $this->drupalGet('taxonomy/term/6');

    // We should see just parents. All descendants must be hidden.
    for ($i = 1; $i < 4; $i++) {
      $this->assertSession()->pageTextContains('Parent term ' . $i);

      for ($n = 1; $n < 12; $n++) {
        $this->assertSession()->pageTextNotContains('Child term ' . $i . '-' . $n);
      }
    }
  }

  /**
   * Test the block with collapsed and selectable content.
   */
  public function testCollapsedAndSelectable() {
    $this->block->set('settings', [
      'label' => 'Hierarchical Taxonomy Menu',
      'label_display' => 'visible',
      'vocabulary' => $this->vocabulary->id() . '|',
      'collapsible' => TRUE,
      'interactive_parent' => TRUE,
    ]);
    $this->block->save();

    $this->drupalGet('<front>');

    // We should see 33 toggle arrows.
    $toggle_elements = $this->getSession()->getPage()->findAll('css', '#block-hierarchicaltaxonomymenu i.arrow-right.parent-toggle');
    $this->assertCount(33, $toggle_elements);
  }

  /**
   * Test the block with no content.
   */
  public function testEmptyBlock() {
    $vocabulary = $this->createVocabulary();

    $this->block->set('settings', [
      'label' => 'Hierarchical Taxonomy Menu',
      'label_display' => 'visible',
      'vocabulary' => $vocabulary->id() . '|',
      'hide_block' => FALSE,
    ]);
    $this->block->save();

    $this->drupalGet('<front>');
    $this->assertSession()->pageTextContains('Hierarchical Taxonomy Menu');

    $this->block->set('settings', [
      'label' => 'Hierarchical Taxonomy Menu',
      'label_display' => 'visible',
      'vocabulary' => $vocabulary->id() . '|',
      'hide_block' => TRUE,
    ]);
    $this->block->save();

    // Empty block should be hidden if 'Hide block if the output is empty' is
    // checked.
    $this->drupalGet('<front>');
    $this->assertSession()->pageTextNotContains('Hierarchical Taxonomy Menu');
  }

}
