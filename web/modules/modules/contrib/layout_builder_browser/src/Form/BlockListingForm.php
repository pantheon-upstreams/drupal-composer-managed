<?php

namespace Drupal\layout_builder_browser\Form;

use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\Html;
use Drupal\Core\Block\BlockManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Builds a listing of block entities.
 */
class BlockListingForm extends FormBase {

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Block manager.
   *
   * @var \Drupal\Core\Block\BlockManagerInterface
   */
  protected $blockManager;

  /**
   * Constructs an layout_builder_browserForm object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entityTypeManager.
   * @param \Drupal\Core\Block\BlockManagerInterface $blockManager
   *   The blockManager.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, BlockManagerInterface $blockManager) {
    $this->entityTypeManager = $entityTypeManager;
    $this->blockManager = $blockManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('plugin.manager.block')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'layout_builder_browser_block_listing';
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header = [
      'title' => [
        'data' => $this->t('Title'),
      ],
      'block_provider' => $this->t('Block provider'),
      'category' => $this->t('Category'),
      'weight' => $this->t('Weight'),
      'status' => $this->t('Status'),
      'operations' => $this->t('Operations'),
    ];
    return $header;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $categories = $this->loadCategories();

    $form['categories'] = [
      '#type' => 'table',
      '#header' => $this->buildHeader(),
      '#empty' => $this->t('No block categories defined.'),
      '#attributes' => [
        'id' => 'blocks',
      ],
    ];

    foreach ($categories as $category) {
      $category_id = $category["category"]->id;

      $form['categories']['category_' . $category_id] = $this->buildBlockCategoryRow($category["category"]);

      $form['categories']['#tabledrag'][] = [
        'action' => 'order',
        'relationship' => 'sibling',
        'group' => 'block-weight',
        'subgroup' => 'block-weight-' . $category_id,
      ];

      $form['categories']['region-' . $category_id . '-message'] = [
        '#attributes' => [
          'class' => [
            'region-message',
            'region-' . $category_id . '-message',
            empty($category['blocks']) ? 'region-empty' : 'region-populated',
          ],
        ],
      ];
      $form['categories']['region-' . $category_id . '-message']['message'] = [
        '#markup' => '<em>' . $this->t('No blocks in this category') . '</em>',
        '#wrapper_attributes' => [
          'colspan' => 5,
        ],
      ];

      foreach ($category['blocks'] as $block) {
        $block['category'] = $category_id;
        $form['categories'][$block['id']] = $this->buildBlockRow($block);
      }
    }

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#button_type' => 'primary',
    ];

    $form['#attached']['library'][] = 'layout_builder_browser/admin';
    $form['#attached']['library'][] = 'block/drupal.block';
    $form['#attached']['library'][] = 'block/drupal.block.admin';

    return $form;
  }

  /**
   * Builds one block row.
   *
   * @var array $block
   *   The block.
   *
   * @return array
   *   Row with information about block.
   */
  private function buildBlockRow($block) {
    $row = [];
    $row['title'] = [
      '#type' => 'markup',
      '#markup' => '<div class="block-title">' . $block['label'] . '</div>',
    ];

    $row['block_provider'] = [
      '#type' => 'markup',
      '#markup' => $block["block_provider"],
    ];

    $block_categories = $this->entityTypeManager
      ->getStorage('layout_builder_browser_blockcat')
      ->loadMultiple();
    uasort($block_categories, [
      'Drupal\Core\Config\Entity\ConfigEntityBase',
      'sort',
    ]);

    $categories_options = [];
    foreach ($block_categories as $block_category) {
      $categories_options[$block_category->id()] = $block_category->label();
    }
    $row['category'] = [
      '#type' => 'select',
      '#options' => $categories_options,
      '#default_value' => $block['category'],
      '#attributes' => [
        'class' => [
          'block-region-select',
          'block-region-' . $block['category'],
        ],

      ],
    ];
    $row['#attributes'] = [
      'title' => $this->t('ID: @name', ['@name' => $block['id']]),
      'class' => [
        'block-wrapper',
        'draggable'
      ],
    ];

    $row['weight'] = [
      '#type' => 'weight',
      '#default_value' => $block['weight'],
      '#delta' => 100,
      '#title' => $this->t('Weight for @block block', ['@block' => $block['label']]),
      '#title_display' => 'invisible',
      '#attributes' => [
        'class' => ['block-weight', 'block-weight-' . $block['category']],
      ],
    ];

    $row['status'] = [
      '#type' => 'markup',
      '#markup' => $block['status'] ? $this->t('Enabled') : $this->t('Disabled'),
    ];

    $row['operations'] = [
      '#type' => 'link',
      '#title' => $this->t('Edit'),
      '#url' => Url::fromRoute('entity.layout_builder_browser_block.edit_form', ['layout_builder_browser_block' => $block['id']]),
      '#attributes' => [
        'class' => ['use-ajax', 'button', 'button--small'],
        'data-dialog-type' => 'modal',
        'data-dialog-options' => Json::encode([
          'width' => 700,
        ]),
      ],
    ];
    return $row;

  }

  /**
   * Builds an array of block categorie for display in the overview.
   */
  private function buildBlockCategoryRow($block_category) {

    return [
      'title' => [
        '#theme_wrappers' => [
          'container' => [
            '#attributes' => ['class' => ['block-category-title', 'region-title__action']],
          ],
        ],
        '#type' => 'link',
        '#prefix' => Html::escape($block_category->label()),
        '#title' => $this->t('Place block <span class="visually-hidden">in %category</span>', ['%category' => Html::escape($block_category->label())]),
        '#url' => Url::fromRoute('entity.layout_builder_browser_block.add_form', [], ['query' => ['blockcat' => $block_category->id()]]),
        '#attributes' => [
          'class' => ['use-ajax', 'button', 'button--small'],
          'data-dialog-type' => 'modal',
          'data-dialog-options' => Json::encode([
            'width' => 700,
          ]),
        ],
        '#wrapper_attributes' => [
          'colspan' => 5,
        ],
      ],
      '#attributes' => [
        'class' => ['region-title', 'region-title-' . $block_category->id()],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $blocks = $form_state->getValue('categories');

    $lb_block_storage = $this->entityTypeManager
      ->getStorage('layout_builder_browser_block');

    foreach ($blocks as $id => $block) {
      $lb_block = $lb_block_storage->load($id);
      $lb_block->weight = $block['weight'];
      $lb_block->category = $block['category'];
      $lb_block->save();
    }

    $this->messenger()->addMessage($this->t('The blocks have been updated.'));
  }

  /**
   * Loads block categories and blocks, grouped by block categories.
   *
   * @return \Drupal\Core\Config\Entity\ConfigEntityInterface[][]
   *   An associative array with two keys:
   *   - categories: All available block categories, each followed by all blocks
   *     attached to it.
   *   - hidden_blocks: All blocks that aren't attached to any block categories.
   */
  public function loadCategories() {

    $block_categories = $this->entityTypeManager
      ->getStorage('layout_builder_browser_blockcat')
      ->loadMultiple();
    uasort($block_categories, [
      'Drupal\Core\Config\Entity\ConfigEntityBase',
      'sort',
    ]);

    $block_categories_group = [];

    foreach ($block_categories as $key => $block_category) {

      $block_categories_group[$key]['category'] = $block_category;
      $block_categories_group[$key]['blocks'] = [];

      $blocks = \Drupal::entityTypeManager()
        ->getStorage('layout_builder_browser_block')
        ->loadByProperties(['category' => $key]);
      uasort($blocks, [
        'Drupal\Core\Config\Entity\ConfigEntityBase',
        'sort',
      ]);

      foreach ($blocks as $block) {
        try {
          $block_definition = \Drupal::service('plugin.manager.block')->getDefinition($block->block_id);

          $item = [];
          $item['id'] = $block->id;
          $item['weight'] = $block->weight;
          $item['label'] = $block->label();
          $item['status'] = $block->status();
          $item['block_provider'] = $block_definition['admin_label'] . " - " . $block_definition["category"];
          $item['block_id'] = $block->block_id;

          $block_categories_group[$key]['blocks'][] = $item;
        }
        catch (PluginNotFoundException $e) {
          $message = $this->t('Configuration contains a block "%label" (@id) in "%category_name" category but the block definition is missing. You should <a href="@url">remove</a> the block from the configuration or fix the definition issue.', [
            '%label' => $block->label(),
            '@id' => $block->block_id,
            '%category_name' => $block_category->label(),
            '@url' => $block->toUrl('delete-form')->toString(),
          ]);
          $this->messenger()->addError([
            '#type' => 'html_tag',
            '#tag' => 'span',
            '#value' => $message,
          ]);
          $this->logger('layout_builder_browser')->error($message);
        }
      }

    }

    return $block_categories_group;
  }

}
