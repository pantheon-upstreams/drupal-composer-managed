<?php

namespace Drupal\Tests\hierarchical_taxonomy_menu\FunctionalJavascript;

use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\taxonomy\Entity\Term;
use Drupal\Tests\block\Traits\BlockCreationTrait;
use Drupal\Tests\field\Traits\EntityReferenceTestTrait;
use Drupal\Tests\node\Traits\NodeCreationTrait;
use Drupal\Tests\taxonomy\Traits\TaxonomyTestTrait;

/**
 * Tests the Hierarchical Taxonomy Menu advanced config options.
 *
 * @group hierarchical_taxonomy_menu
 */
class HierarchicalTaxonomyMenuAdvancedTest extends WebDriverTestBase {

  use BlockCreationTrait;
  use TaxonomyTestTrait;
  use NodeCreationTrait;
  use EntityReferenceTestTrait;

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

    $this->drupalCreateContentType(['type' => 'article', 'name' => 'Article']);

    // Create a term reference field on node.
    $this->createEntityReferenceField(
      'node',
      'article',
      'field_tags',
      'Term reference',
      'taxonomy_term',
      'default',
      ['target_bundles' => [$this->vocabulary->id()]],
      FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED
    );

    $this->block = $this->drupalPlaceBlock('hierarchical_taxonomy_menu', [
      'region' => 'content',
      'label' => 'Hierarchical Taxonomy Menu',
      'id' => 'hierarchicaltaxonomymenu',
    ]);
  }

  /**
   * Test base term.
   */
  public function testBaseTerm() {
    $this->block->set('settings', [
      'label' => 'Hierarchical Taxonomy Menu',
      'label_display' => 'visible',
      'vocabulary' => $this->vocabulary->id() . '|',
      'base_term' => 3,
    ]);
    $this->block->save();

    $this->drupalGet('<front>');

    for ($i = 1; $i < 4; $i++) {
      $this->assertSession()->pageTextNotContains('Parent term ' . $i);

      for ($n = 1; $n < 12; $n++) {
        if ($i == 1 && $n > 2) {
          $this->assertSession()->pageTextContains('Child term ' . $i . '-' . $n);
        }
        else {
          // We need that empty space at the end because Child term 1-10 and
          // Child term 1-11 contain substring Child term 1-1.
          $this->assertSession()->pageTextNotContains('Child term ' . $i . '-' . $n . ' ');
        }
      }
    }
  }

  /**
   * Test dynamic base term.
   */
  public function testDynamicBaseTerm() {
    $this->block->set('settings', [
      'label' => 'Hierarchical Taxonomy Menu',
      'label_display' => 'visible',
      'vocabulary' => $this->vocabulary->id() . '|',
      'dynamic_base_term' => TRUE,
    ]);
    $this->block->save();

    $this->drupalGet('<front>');
    // In this case we should see all terms.
    for ($i = 1; $i < 4; $i++) {
      $this->assertSession()->pageTextContains('Parent term ' . $i);

      for ($n = 1; $n < 12; $n++) {
        $this->assertSession()->pageTextContains('Child term ' . $i . '-' . $n);
      }
    }

    $this->drupalGet('taxonomy/term/1');
    // In this case we should see just Term #1 children.
    for ($i = 1; $i < 4; $i++) {
      $this->assertSession()->pageTextNotContains('Parent term ' . $i);

      for ($n = 1; $n < 12; $n++) {
        if ($i == 1) {
          $this->assertSession()->pageTextContains('Child term ' . $i . '-' . $n);
        }
        else {
          $this->assertSession()->pageTextNotContains('Child term ' . $i . '-' . $n);
        }
      }
    }

    $this->drupalGet('taxonomy/term/13');
    // In this case we should see just Term #13 children.
    for ($i = 1; $i < 4; $i++) {
      $this->assertSession()->pageTextNotContains('Parent term ' . $i);

      for ($n = 1; $n < 12; $n++) {
        if ($i == 2) {
          $this->assertSession()->pageTextContains('Child term ' . $i . '-' . $n);
        }
        else {
          $this->assertSession()->pageTextNotContains('Child term ' . $i . '-' . $n);
        }
      }
    }

    $this->drupalGet('taxonomy/term/25');
    // In this case we should see just Term #25 children.
    for ($i = 1; $i < 4; $i++) {
      $this->assertSession()->pageTextNotContains('Parent term ' . $i);

      for ($n = 1; $n < 12; $n++) {
        if ($i == 3) {
          $this->assertSession()->pageTextContains('Child term ' . $i . '-' . $n);
        }
        else {
          $this->assertSession()->pageTextNotContains('Child term ' . $i . '-' . $n);
        }
      }
    }

    $this->drupalGet('taxonomy/term/33');
    // In this case we should see just Term #33 children.
    for ($i = 1; $i < 4; $i++) {
      $this->assertSession()->pageTextNotContains('Parent term ' . $i);

      for ($n = 1; $n < 12; $n++) {
        if ($i == 3 && $n > 8) {
          $this->assertSession()->pageTextContains('Child term ' . $i . '-' . $n);
        }
        else {
          // We need that empty space at the end because Child term 3-10 and
          // Child term 3-11 contain substring Child term 3-1.
          $this->assertSession()->pageTextNotContains('Child term ' . $i . '-' . $n . ' ');
        }
      }
    }
  }

  /**
   * Test count of referencing nodes.
   */
  public function testCountOfReferencingNodes() {
    $this->block->set('settings', [
      'label' => 'Hierarchical Taxonomy Menu',
      'label_display' => 'visible',
      'vocabulary' => $this->vocabulary->id() . '|',
      'show_count' => TRUE,
    ]);
    $this->block->save();

    $this->drupalGet('<front>');

    for ($i = 1; $i < 4; $i++) {
      $this->assertSession()->pageTextContains('Parent term ' . $i . ' [0]');

      for ($n = 1; $n < 12; $n++) {
        $this->assertSession()->pageTextContains('Child term ' . $i . '-' . $n . ' [0]');
      }
    }

    $term = Term::load(1);
    $this->drupalCreateNode([
      'type' => 'article',
      'title' => 'Article 1',
      'field_tags' => $term,
    ]);

    $term = Term::load(2);
    $this->drupalCreateNode([
      'type' => 'article',
      'title' => 'Article 2',
      'field_tags' => $term,
    ]);

    $term = Term::load(34);
    $this->drupalCreateNode([
      'type' => 'article',
      'title' => 'Article 3',
      'field_tags' => $term,
    ]);
    $this->drupalCreateNode([
      'type' => 'article',
      'title' => 'Article 4',
      'field_tags' => $term,
    ]);

    $this->drupalGet('<front>');

    for ($i = 1; $i < 4; $i++) {
      if ($i == 1) {
        $this->assertSession()->pageTextContains('Parent term ' . $i . ' [1]');
      }
      else {
        $this->assertSession()->pageTextContains('Parent term ' . $i . ' [0]');
      }

      for ($n = 1; $n < 12; $n++) {
        if ($i == 1 && $n == 1) {
          $this->assertSession()->pageTextContains('Child term ' . $i . '-' . $n . ' [1]');
        }
        elseif ($i == 3 && $n == 9) {
          $this->assertSession()->pageTextContains('Child term ' . $i . '-' . $n . ' [2]');
        }
        else {
          $this->assertSession()->pageTextContains('Child term ' . $i . '-' . $n . ' [0]');
        }
      }
    }
  }

  /**
   * Test recursively count of referencing nodes.
   */
  public function testRecursivelyCountOfReferencingNodes() {
    $this->block->set('settings', [
      'label' => 'Hierarchical Taxonomy Menu',
      'label_display' => 'visible',
      'vocabulary' => $this->vocabulary->id() . '|',
      'show_count' => TRUE,
      'calculate_count_recursively' => TRUE,
    ]);
    $this->block->save();

    $this->drupalGet('<front>');

    for ($i = 1; $i < 4; $i++) {
      $this->assertSession()->pageTextContains('Parent term ' . $i . ' [0]');

      for ($n = 1; $n < 12; $n++) {
        $this->assertSession()->pageTextContains('Child term ' . $i . '-' . $n . ' [0]');
      }
    }

    $term = Term::load(1);
    $this->drupalCreateNode([
      'type' => 'article',
      'title' => 'Article 1',
      'field_tags' => $term,
    ]);

    $term = Term::load(2);
    $this->drupalCreateNode([
      'type' => 'article',
      'title' => 'Article 2',
      'field_tags' => $term,
    ]);

    $term = Term::load(3);
    $this->drupalCreateNode([
      'type' => 'article',
      'title' => 'Article 3',
      'field_tags' => $term,
    ]);
    $this->drupalCreateNode([
      'type' => 'article',
      'title' => 'Article 4',
      'field_tags' => $term,
    ]);

    $term = Term::load(4);
    $this->drupalCreateNode([
      'type' => 'article',
      'title' => 'Article 5',
      'field_tags' => $term,
    ]);
    $this->drupalCreateNode([
      'type' => 'article',
      'title' => 'Article 6',
      'field_tags' => $term,
    ]);

    $this->drupalGet('<front>');

    for ($i = 1; $i < 4; $i++) {
      if ($i == 1) {
        $this->assertSession()->pageTextContains('Parent term ' . $i . ' [6]');
      }
      else {
        $this->assertSession()->pageTextContains('Parent term ' . $i . ' [0]');
      }

      for ($n = 1; $n < 12; $n++) {
        if ($i == 1 && $n == 1) {
          $this->assertSession()->pageTextContains('Child term ' . $i . '-' . $n . ' [5]');
        }
        elseif ($i == 1 && $n == 2) {
          $this->assertSession()->pageTextContains('Child term ' . $i . '-' . $n . ' [4]');
        }
        elseif ($i == 1 && $n == 3) {
          $this->assertSession()->pageTextContains('Child term ' . $i . '-' . $n . ' [2]');
        }
        else {
          $this->assertSession()->pageTextContains('Child term ' . $i . '-' . $n . ' [0]');
        }
      }
    }
  }

}
