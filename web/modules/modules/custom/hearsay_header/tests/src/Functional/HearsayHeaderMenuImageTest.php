<?php

namespace Drupal\Tests\hearsay_header\Functional;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\file\Entity\File;
use Drupal\Tests\block\Traits\BlockCreationTrait;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\taxonomy\Traits\TaxonomyTestTrait;
use Drupal\Tests\TestFileCreationTrait;

/**
 * Tests the Hearsay Header Menu images.
 *
 * @group hearsay_header_menu
 */
class HearsayHeaderMenuImageTest extends BrowserTestBase
{

    use BlockCreationTrait;
    use TaxonomyTestTrait;
    use TestFileCreationTrait;

    /**
     * Modules to enable.
     *
     * @var array
     */
    public static $modules = [
    'block',
    'image',
    'hearsay_header_menu',
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
     * The vocabulary.
     *
     * @var \Drupal\taxonomy\VocabularyInterface
     */
    protected $vocabulary;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $vocabulary = $this->createVocabulary();
        $this->vocabulary = $vocabulary;

        FieldStorageConfig::create(
            [
            'field_name' => 'field_icon',
            'entity_type' => 'taxonomy_term',
            'type' => 'image',
            'settings' => [],
            'cardinality' => 1,
            ]
        )->save();

        $field_config = FieldConfig::create(
            [
            'field_name' => 'field_icon',
            'label' => 'Icon',
            'entity_type' => 'taxonomy_term',
            'bundle' => $vocabulary->id(),
            'required' => true,
            'settings' => [],
            'description' => '',
            ]
        );
        $field_config->save();

        $images = $this->getTestFiles('image');

        $file1 = File::create(
            [
            'uri' => $images[0]->uri,
            'status' => FILE_STATUS_PERMANENT,
            ]
        );
        $file1->save();

        $file2 = File::create(
            [
            'uri' => $images[1]->uri,
            'status' => FILE_STATUS_PERMANENT,
            ]
        );
        $file2->save();

        $this->createTerm(
            $vocabulary, [
            'name' => 'Term 1',
            'field_icon' => [
            'target_id' => $file1->id(),
            'alt' => 'First image',
            ],
            ]
        );

        $this->createTerm(
            $vocabulary, [
            'name' => 'Term 2',
            'field_icon' => [
            'target_id' => $file2->id(),
            'alt' => 'Second image',
            ],
            ]
        );

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
            'vocabulary' => $vocabulary->id() . '|field_icon',
            'collapsible' => false,
            ]
        );
        $block->save();
        $this->block = $block;
    }

    /**
     * Tests that images are displayed.
     */
    public function testBlockImagesEnabled()
    {
        $this->drupalGet('<front>');
        $block_element = $this->getSession()->getPage()->find('css', '.block-taxonomymenu__menu');
        $this->assertStringContainsString('Term 1', $block_element->getText());
        $this->assertStringContainsString('Term 2', $block_element->getText());
        $this->assertSession()->elementAttributeContains('css', '.hearsay-header-menu li:first-child img', 'alt', 'Term 1');
        $this->assertSession()->elementAttributeContains('css', '.hearsay-header-menu li:first-child img', 'height', '16');
        $this->assertSession()->elementAttributeContains('css', '.hearsay-header-menu li:first-child img', 'width', '16');
        $this->assertSession()->elementAttributeContains('css', '.hearsay-header-menu li:last-child img', 'alt', 'Term 2');
        $this->assertSession()->elementAttributeContains('css', '.hearsay-header-menu li:last-child img', 'height', '16');
        $this->assertSession()->elementAttributeContains('css', '.hearsay-header-menu li:last-child img', 'width', '16');
    }

    /**
     * Tests image dimensions.
     */
    public function testBlockImageDimensions()
    {
        $this->block->set(
            'settings', [
            'label' => 'Hearsay Header Menu',
            'label_display' => 'visible',
            'vocabulary' => $this->vocabulary->id() . '|field_icon',
            'collapsible' => false,
            'image_height' => 128,
            'image_width' => 256,
            ]
        )->save();

        $this->drupalGet('<front>');
        $this->assertSession()->elementAttributeContains('css', '.hearsay-header-menu li:first-child img', 'height', '128');
        $this->assertSession()->elementAttributeContains('css', '.hearsay-header-menu li:first-child img', 'width', '256');
        $this->assertSession()->elementAttributeContains('css', '.hearsay-header-menu li:last-child img', 'height', '128');
        $this->assertSession()->elementAttributeContains('css', '.hearsay-header-menu li:last-child img', 'width', '256');
    }

    /**
     * Tests image styles.
     */
    public function testBlockImageStyles()
    {
        $this->block->set(
            'settings', [
            'label' => 'Hearsay Header Menu',
            'label_display' => 'visible',
            'vocabulary' => $this->vocabulary->id() . '|field_icon',
            'collapsible' => false,
            'use_image_style' => true,
            'image_style' => 'medium',
            ]
        )->save();

        $this->drupalGet('<front>');
        $image1 = $this->getSession()->getPage()->find('css', '.hearsay-header-menu li:first-child img');
        $this->assertStringContainsString('files/styles/medium', $image1->getAttribute('src'));
        $image2 = $this->getSession()->getPage()->find('css', '.hearsay-header-menu li:last-child img');
        $this->assertStringContainsString('files/styles/medium', $image2->getAttribute('src'));
    }

    /**
     * Tests that images are not displayed.
     */
    public function testBlockImagesDisabled()
    {
        $this->block->set(
            'settings', [
            'label' => 'Hearsay Header Menu',
            'label_display' => 'visible',
            'vocabulary' => $this->vocabulary->id() . '|',
            'collapsible' => false,
            ]
        )->save();

        $this->drupalGet('<front>');
        $block_element = $this->getSession()->getPage()->find('css', '.block-taxonomymenu__menu');
        $this->assertStringContainsString('Term 1', $block_element->getText());
        $this->assertStringContainsString('Term 2', $block_element->getText());
        $this->assertSession()->elementNotExists('css', '.hearsay-header-menu li:first-child img');
        $this->assertSession()->elementNotExists('css', '.hearsay-header-menu li:last-child img');
    }

}
