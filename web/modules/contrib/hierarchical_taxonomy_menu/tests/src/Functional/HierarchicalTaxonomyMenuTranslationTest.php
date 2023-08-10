<?php

namespace Drupal\Tests\hierarchical_taxonomy_menu\Functional;

use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\taxonomy\Entity\Term;
use Drupal\Tests\block\Traits\BlockCreationTrait;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\taxonomy\Traits\TaxonomyTestTrait;

/**
 * Tests the Hierarchical Taxonomy Menu block with translated taxonomy terms.
 *
 * @group hierarchical_taxonomy_menu
 */
class HierarchicalTaxonomyMenuTranslationTest extends BrowserTestBase {

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
    'locale',
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

    $language = ConfigurableLanguage::createFromLangcode('sr');
    $language->save();

    $admin_user = $this->drupalCreateUser([
      'administer blocks',
      'administer site configuration',
      'access administration pages',
    ]);

    $this->drupalLogin($admin_user);

    $vocabulary = $this->createVocabulary();

    $parent_term = $this->createTerm($vocabulary, [
      'name' => 'Parent',
      'langcode' => 'en',
      'status' => TRUE,
    ]);
    $child_term = $this->createTerm($vocabulary, [
      'name' => 'Child',
      'langcode' => 'en',
      'status' => TRUE,
    ]);
    $child_term->parent = $parent_term->id();
    $child_term->save();

    $parent_term_sr = $parent_term->addTranslation('sr');
    $parent_term_sr->name = 'Roditelj';
    $parent_term_sr->langcode = 'sr';
    $parent_term_sr->status = TRUE;
    $parent_term_sr->save();

    $child_term_sr = $child_term->addTranslation('sr');
    $child_term_sr->name = 'Dete';
    $child_term_sr->langcode = 'sr';
    $child_term_sr->status = TRUE;
    $child_term_sr->save();

    $block = $this->drupalPlaceBlock('hierarchical_taxonomy_menu', [
      'region' => 'content',
      'label' => 'Hierarchical Taxonomy Menu',
      'id' => 'hierarchicaltaxonomymenu',
    ]);

    $block->set('settings', [
      'label' => 'Hierarchical Taxonomy Menu',
      'label_display' => 'visible',
      'vocabulary' => $vocabulary->id() . '|',
      'dynamic_block_title' => TRUE,
    ]);
    $block->save();
  }

  /**
   * Test translated block content.
   */
  public function testTranslatedBlockContent() {
    $this->drupalGet('taxonomy/term/1');
    $block_element = $this->getSession()->getPage()->find('css', '.block-taxonomymenu__menu');
    $this->assertStringContainsString('Parent', $block_element->getText());
    $this->assertStringContainsString('Child', $block_element->getText());

    $this->drupalGet('sr/taxonomy/term/1');
    $block_element = $this->getSession()->getPage()->find('css', '.block-taxonomymenu__menu');
    $this->assertStringContainsString('Roditelj', $block_element->getText());
    $this->assertStringContainsString('Dete', $block_element->getText());
  }

  /**
   * Test translated terms status.
   */
  public function testTranslatedBlockContentWithDisabledChild() {
    $this->drupalGet('taxonomy/term/1');
    $block_element = $this->getSession()->getPage()->find('css', '.block-taxonomymenu__menu');
    $this->assertStringContainsString('Parent', $block_element->getText());
    $this->assertStringContainsString('Child', $block_element->getText());

    $parent_term = Term::load(1);
    $parent_term->status = FALSE;
    $parent_term->save();
    $this->drupalGet('taxonomy/term/1');
    $block_element = $this->getSession()->getPage()->find('css', '.block-taxonomymenu__menu');
    $this->assertStringNotContainsString('Parent', $block_element->getText());

    $child_term = Term::load(2);
    $child_term->status = FALSE;
    $child_term->save();
    $this->drupalGet('taxonomy/term/1');
    $block_element = $this->getSession()->getPage()->find('css', '.block-taxonomymenu__menu');
    $this->assertStringNotContainsString('Child', $block_element->getText());

    $this->drupalGet('sr/taxonomy/term/1');
    $block_element = $this->getSession()->getPage()->find('css', '.block-taxonomymenu__menu');
    $this->assertStringContainsString('Roditelj', $block_element->getText());
    $this->assertStringContainsString('Dete', $block_element->getText());

    $parent_term = Term::load(1);
    $parent_term_sr = \Drupal::service('entity.repository')
      ->getTranslationFromContext($parent_term, 'sr');
    $parent_term_sr->status = FALSE;
    $parent_term_sr->save();
    $this->drupalGet('sr/taxonomy/term/1');
    $block_element = $this->getSession()->getPage()->find('css', '.block-taxonomymenu__menu');
    $this->assertStringNotContainsString('Roditelj', $block_element->getText());

    $child_term = Term::load(2);
    $child_term_sr = \Drupal::service('entity.repository')
      ->getTranslationFromContext($child_term, 'sr');
    $child_term_sr->status = FALSE;
    $child_term_sr->save();
    $this->drupalGet('sr/taxonomy/term/1');
    $block_element = $this->getSession()->getPage()->find('css', '.block-taxonomymenu__menu');
    $this->assertStringNotContainsString('Dete', $block_element->getText());
  }

  /**
   * Test dynamic block title.
   */
  public function testDynamicBlockTitle() {
    $this->drupalGet('taxonomy/term/1');
    $block_title_element = $this->getSession()->getPage()->find('css', '#block-hierarchicaltaxonomymenu h2');
    $this->assertEqual($block_title_element->getText(), 'Parent');

    $this->drupalGet('sr/taxonomy/term/1');
    $block_title_element = $this->getSession()->getPage()->find('css', '#block-hierarchicaltaxonomymenu h2');
    $this->assertEqual($block_title_element->getText(), 'Roditelj');
  }

}
