<?php

namespace Drupal\Tests\hierarchical_taxonomy_menu\Functional;

use Drupal\taxonomy\Entity\Term;
use Drupal\Tests\block\Traits\BlockCreationTrait;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\system\Functional\Cache\AssertPageCacheContextsAndTagsTrait;
use Drupal\Tests\taxonomy\Traits\TaxonomyTestTrait;

/**
 * Tests the Hierarchical Taxonomy Menu block caching.
 *
 * @group hierarchical_taxonomy_menu
 */
class HierarchicalTaxonomyMenuCacheTest extends BrowserTestBase {

  use BlockCreationTrait;
  use TaxonomyTestTrait;
  use AssertPageCacheContextsAndTagsTrait;

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
   * The block.
   *
   * @var \Drupal\block\Entity\Block
   */
  protected $block;

  /**
   * The user.
   *
   * @var \Drupal\user\Entity\User
   */
  protected $user;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $vocabulary = $this->createVocabulary();

    $this->createTerm($vocabulary, [
      'name' => 'Term 1',
    ]);

    $this->createTerm($vocabulary, [
      'name' => 'Term 2',
    ]);

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

    $this->block = $block;

    $user = $this->drupalCreateUser([
      'access content',
    ]);

    $this->user = $user;
  }

  /**
   * Tests cache context for anonymous users.
   */
  public function testBlockCacheContextAnonymous() {
    $this->drupalGet('<front>');
    $this->assertCacheContext('url.path');

    $this->drupalGet('taxonomy/term/1');
    $this->assertCacheContext('url.path');
    $block_title_element = $this->getSession()->getPage()->find('css', '#block-hierarchicaltaxonomymenu h2');
    $this->assertEqual($block_title_element->getText(), 'Term 1');

    $this->drupalGet('taxonomy/term/2');
    $this->assertCacheContext('url.path');
    $block_title_element = $this->getSession()->getPage()->find('css', '#block-hierarchicaltaxonomymenu h2');
    $this->assertEqual($block_title_element->getText(), 'Term 2');
  }

  /**
   * Tests cache context for authenticated users.
   */
  public function testBlockCacheContextAuthenticated() {
    $this->drupalLogin($this->user);

    $this->drupalGet('<front>');
    $this->assertCacheContext('url.path');

    $this->drupalGet('taxonomy/term/1');
    $this->assertCacheContext('url.path');
    $block_title_element = $this->getSession()->getPage()->find('css', '#block-hierarchicaltaxonomymenu h2');
    $this->assertEqual($block_title_element->getText(), 'Term 1');

    $this->drupalGet('taxonomy/term/2');
    $this->assertCacheContext('url.path');
    $block_title_element = $this->getSession()->getPage()->find('css', '#block-hierarchicaltaxonomymenu h2');
    $this->assertEqual($block_title_element->getText(), 'Term 2');
  }

  /**
   * Tests that 'taxonomy_term_list' tag is working for anonymous users.
   */
  public function testBlockCacheTagsAnonymous() {
    $this->drupalGet('<front>');
    $block_element = $this->getSession()->getPage()->find('css', '.block-taxonomymenu__menu');
    $this->assertCacheTags(array_merge($this->block->getCacheTags(), [
      'block_view',
      'config:block_list',
      'config:system.site',
      'http_response',
      'rendered',
      'taxonomy_term_list',
    ]));
    $this->assertStringContainsString('Term 1', $block_element->getText());
    $this->assertStringContainsString('Term 2', $block_element->getText());

    $term1 = Term::load(1);
    $term1->name->value = 'Renamed 1';
    $term1->save();

    $term2 = Term::load(2);
    $term2->name->value = 'Renamed 2';
    $term2->save();

    $this->drupalGet('<front>');
    $block_element = $this->getSession()->getPage()->find('css', '.block-taxonomymenu__menu');
    $this->assertCacheTags(array_merge($this->block->getCacheTags(), [
      'block_view',
      'config:block_list',
      'config:system.site',
      'http_response',
      'rendered',
      'taxonomy_term_list',
    ]));
    $this->assertStringContainsString('Renamed 1', $block_element->getText());
    $this->assertStringContainsString('Renamed 2', $block_element->getText());
  }

  /**
   * Tests that 'taxonomy_term_list' tag is working for authenticated users.
   */
  public function testBlockCacheTagsAuthenticated() {
    $this->drupalLogin($this->user);

    $this->drupalGet('<front>');
    $block_element = $this->getSession()->getPage()->find('css', '.block-taxonomymenu__menu');
    $this->assertCacheTags(array_merge($this->block->getCacheTags(), [
      'block_view',
      'config:block_list',
      'http_response',
      'rendered',
      'taxonomy_term_list',
      'user_view',
      'user:' . $this->user->id(),
    ]));
    $this->assertStringContainsString('Term 1', $block_element->getText());
    $this->assertStringContainsString('Term 2', $block_element->getText());

    $term1 = Term::load(1);
    $term1->name->value = 'Re-renamed 1';
    $term1->save();

    $term2 = Term::load(2);
    $term2->name->value = 'Re-renamed 2';
    $term2->save();

    $this->drupalGet('<front>');
    $block_element = $this->getSession()->getPage()->find('css', '.block-taxonomymenu__menu');
    $this->assertCacheTags(array_merge($this->block->getCacheTags(), [
      'block_view',
      'config:block_list',
      'http_response',
      'rendered',
      'taxonomy_term_list',
      'user_view',
      'user:' . $this->user->id(),
    ]));
    $this->assertStringContainsString('Re-renamed 1', $block_element->getText());
    $this->assertStringContainsString('Re-renamed 2', $block_element->getText());
  }

}
