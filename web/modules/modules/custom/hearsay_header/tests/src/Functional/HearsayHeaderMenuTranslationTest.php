<?php

namespace Drupal\Tests\hearsay_header\Functional;

use Drupal\language\Entity\ConfigurableLanguage;
use Drupal\taxonomy\Entity\Term;
use Drupal\Tests\block\Traits\BlockCreationTrait;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\taxonomy\Traits\TaxonomyTestTrait;

/**
 * Tests the Hearsay Header Menu block with translated taxonomy terms.
 *
 * @group hearsay_header_menu
 */
class HearsayHeaderMenuTranslationTest extends BrowserTestBase
{

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
    'hearsay_header_menu',
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
    protected function setUp()
    {
        parent::setUp();

        $language = ConfigurableLanguage::createFromLangcode('sr');
        $language->save();

        $admin_user = $this->drupalCreateUser(
            [
            'administer blocks',
            'administer site configuration',
            'access administration pages',
            ]
        );

        $this->drupalLogin($admin_user);

        $vocabulary = $this->createVocabulary();

        $parent_term = $this->createTerm(
            $vocabulary, [
            'name' => 'Parent',
            'langcode' => 'en',
            'status' => true,
            ]
        );
        $child_term = $this->createTerm(
            $vocabulary, [
            'name' => 'Child',
            'langcode' => 'en',
            'status' => true,
            ]
        );
        $child_term->parent = $parent_term->id();
        $child_term->save();

        $parent_term_sr = $parent_term->addTranslation('sr');
        $parent_term_sr->name = 'Roditelj';
        $parent_term_sr->langcode = 'sr';
        $parent_term_sr->status = true;
        $parent_term_sr->save();

        $child_term_sr = $child_term->addTranslation('sr');
        $child_term_sr->name = 'Dete';
        $child_term_sr->langcode = 'sr';
        $child_term_sr->status = true;
        $child_term_sr->save();

        $block = $this->drupalPlaceBlock(
            'hearsay_header_menu', [
            'region' => 'content',
            'label' => 'Hearsay Header Menu',
            'id' => 'hearsayheadermenu',
            ]
        );

        $block->set(
            'settings', [
            'label' => 'Hearsay Header Menu',
            'label_display' => 'visible',
            'vocabulary' => $vocabulary->id() . '|',
            'dynamic_block_title' => true,
            ]
        );
        $block->save();
    }

    /**
     * Test translated block content.
     */
    public function testTranslatedBlockContent()
    {
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
    public function testTranslatedBlockContentWithDisabledChild()
    {
        $this->drupalGet('taxonomy/term/1');
        $block_element = $this->getSession()->getPage()->find('css', '.block-taxonomymenu__menu');
        $this->assertStringContainsString('Parent', $block_element->getText());
        $this->assertStringContainsString('Child', $block_element->getText());

        $parent_term = Term::load(1);
        $parent_term->status = false;
        $parent_term->save();
        $this->drupalGet('taxonomy/term/1');
        $block_element = $this->getSession()->getPage()->find('css', '.block-taxonomymenu__menu');
        $this->assertStringNotContainsString('Parent', $block_element->getText());

        $child_term = Term::load(2);
        $child_term->status = false;
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
        $parent_term_sr->status = false;
        $parent_term_sr->save();
        $this->drupalGet('sr/taxonomy/term/1');
        $block_element = $this->getSession()->getPage()->find('css', '.block-taxonomymenu__menu');
        $this->assertStringNotContainsString('Roditelj', $block_element->getText());

        $child_term = Term::load(2);
        $child_term_sr = \Drupal::service('entity.repository')
        ->getTranslationFromContext($child_term, 'sr');
        $child_term_sr->status = false;
        $child_term_sr->save();
        $this->drupalGet('sr/taxonomy/term/1');
        $block_element = $this->getSession()->getPage()->find('css', '.block-taxonomymenu__menu');
        $this->assertStringNotContainsString('Dete', $block_element->getText());
    }

    /**
     * Test dynamic block title.
     */
    public function testDynamicBlockTitle()
    {
        $this->drupalGet('taxonomy/term/1');
        $block_title_element = $this->getSession()->getPage()->find('css', '#block-hearsayheadermenu h2');
        $this->assertEqual($block_title_element->getText(), 'Parent');

        $this->drupalGet('sr/taxonomy/term/1');
        $block_title_element = $this->getSession()->getPage()->find('css', '#block-hearsayheadermenu h2');
        $this->assertEqual($block_title_element->getText(), 'Roditelj');
    }

}
