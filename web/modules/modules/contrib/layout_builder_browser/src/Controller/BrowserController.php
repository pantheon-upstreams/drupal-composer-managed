<?php

namespace Drupal\layout_builder_browser\Controller;

use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\Html;
use Drupal\Core\Ajax\AjaxHelperTrait;
use Drupal\Core\Block\BlockManagerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\layout_builder\Context\LayoutBuilderContextTrait;
use Drupal\layout_builder\Controller\ChooseBlockController;
use Drupal\layout_builder\LayoutBuilderHighlightTrait;
use Drupal\layout_builder\SectionStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BrowserController.
 */
class BrowserController extends ControllerBase {

  use AjaxHelperTrait;
  use LayoutBuilderContextTrait;
  use LayoutBuilderHighlightTrait;
  use StringTranslationTrait;


  /**
   * The block manager.
   *
   * @var \Drupal\Core\Block\BlockManagerInterface
   */
  protected $blockManager;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity_type.bundle.info service.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $entityTypeBundleInfo;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The module handler to invoke the alter hook.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * BrowserController constructor.
   *
   * @param \Drupal\Core\Block\BlockManagerInterface $block_manager
   *   The block manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle info.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   */
  public function __construct(BlockManagerInterface $block_manager, EntityTypeManagerInterface $entity_type_manager, EntityTypeBundleInfoInterface $entity_type_bundle_info, ModuleHandlerInterface $module_handler, AccountInterface $current_user = NULL) {
    $this->blockManager = $block_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
    $this->moduleHandler = $module_handler;
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.block'),
      $container->get('entity_type.manager'),
      $container->get('entity_type.bundle.info'),
      $container->get('module_handler'),
      $container->get('current_user')
    );
  }

  /**
   * Overrides the core ChooseBlockController::build.
   */
  public function browse(SectionStorageInterface $section_storage, $delta, $region) {
    $config = $this->config('layout_builder_browser.settings');
    $enabled_section_storages = $config->get('enabled_section_storages');

    if (!in_array($section_storage->getPluginId(), $enabled_section_storages)) {
      $default_choose_block_controller = new ChooseBlockController($this->blockManager, $this->entityTypeManager, $this->currentUser);
      return $default_choose_block_controller->build($section_storage, $delta, $region);
    }

    $build['filter'] = [
      '#type' => 'search',
      '#title' => $this->t('Filter by block name'),
      '#title_display' => 'invisible',
      '#size' => 30,
      '#placeholder' => $this->t('Filter by block name'),
      '#attributes' => [
        'class' => ['js-layout-builder-filter'],
        'title' => $this->t('Enter a part of the block name to filter by.'),
      ],
    ];

    $block_categories['#type'] = 'container';
    $block_categories['#attributes']['class'][] = 'block-categories';
    $block_categories['#attributes']['class'][] = 'js-layout-builder-categories';
    $block_categories['#attributes']['data-layout-builder-target-highlight-id'] = $this->blockAddHighlightId($delta, $region);

    // @todo Explicitly cast delta to an integer, remove this in
    //   https://www.drupal.org/project/drupal/issues/2984509.
    $delta = (int) $delta;

    $definitions = $this->blockManager->getFilteredDefinitions('layout_builder', $this->getPopulatedContexts($section_storage), [
      'section_storage' => $section_storage,
      'delta' => $delta,
      'region' => $region,
      'list' => 'inline_blocks',
      'browse' => TRUE,
    ]);

    $blockcats = $this->entityTypeManager
      ->getStorage('layout_builder_browser_blockcat')
      ->loadByProperties(['status' => TRUE]);
    uasort($blockcats, ['Drupal\Core\Config\Entity\ConfigEntityBase', 'sort']);

    /** @var \Drupal\layout_builder_browser\Entity\LayoutBuilderBrowserBlockCategory $blockcat */
    foreach ($blockcats as $blockcat) {
      $blocks = [];

      $items = $this->entityTypeManager
        ->getStorage('layout_builder_browser_block')
        ->loadByProperties([
          'category' => $blockcat->id,
          'status' => TRUE,
        ]);
      uasort($items, ['Drupal\Core\Config\Entity\ConfigEntityBase', 'sort']);

      /** @var \Drupal\layout_builder_browser\Entity\LayoutBuilderBrowserBlock $item */
      foreach ($items as $item) {
        $key = $item->block_id;
        if (isset($definitions[$key])) {
          $blocks[$key] = $definitions[$key];
          $blocks[$key]['layout_builder_browser_data'] = $item;
        }
      }

      $block_categories[$blockcat->id()]['links'] = $this->getBlocks($section_storage, $delta, $region, $blocks);
      if ($block_categories[$blockcat->id()]['links']) {
        // Only add the information if the category has links.
        $block_categories[$blockcat->id()]['#type'] = 'details';
        $block_categories[$blockcat->id()]['#attributes']['class'][] = 'js-layout-builder-category';
        $block_categories[$blockcat->id()]['#open'] = TRUE;
        $block_categories[$blockcat->id()]['#title'] = Html::escape($blockcat->label());
      }
      else {
        // Since the category doesn't have links, remove it to avoid confusion.
        unset($block_categories[$blockcat->id()]);
      }
    }

    // Special case for auto adding of reusable content blocks by bundle.
    $auto_added_reusable_bundles = $config->get('auto_added_reusable_block_content_bundles') ?? [];
    $bundles = $this->entityTypeBundleInfo->getBundleInfo('block_content');
    $existing_blocks = $this->entityTypeManager->getStorage('layout_builder_browser_block')
      ->loadMultiple();
    $existing_blocks_ids = array_column($existing_blocks, 'block_id');
    foreach ($auto_added_reusable_bundles as $machine_name) {
      $blocks = [];
      $content_blocks = $this->entityTypeManager->getStorage('block_content')
        ->loadByProperties([
          'type' => $machine_name,
          'reusable' => TRUE,
        ]);
      foreach ($content_blocks as $block) {
        $block_plugin_id = 'block_content:' . $block->uuid();
        // Only blocks available in layout definition and not selected in the
        // browser categories yet.
        if (!empty($definitions[$block_plugin_id]) && !in_array($block_plugin_id, $existing_blocks_ids)) {
          $blocks[$block_plugin_id] = $definitions[$block_plugin_id];
        }
      }
      if ($blocks) {
        $block_links = $this->getBlocks($section_storage, $delta, $region, $blocks);
        if ($block_links) {
          $bundle_label = $bundles[$machine_name]['label'];
          // Only add the information if the category has links.
          $block_categories[$bundle_label]['links'] = $block_links;
          $block_categories[$bundle_label]['#type'] = 'details';
          $block_categories[$bundle_label]['#attributes']['class'][] = 'js-layout-builder-category';
          $block_categories[$bundle_label]['#open'] = TRUE;
          $block_categories[$bundle_label]['#title'] = $this->t('Reusable @block_type_label', ['@block_type_label' => $bundle_label]);
        }
      }
    }

    $build['block_categories'] = $block_categories;
    $build['#attached']['library'][] = 'layout_builder_browser/browser';

    if ($config->get('use_modal')) {
      $build['#attached']['library'][] = 'layout_builder_browser/modal';
    }

    // Allow modules to alter the browser. Provide the context of the current
    // browser in case module want to act differently.
    $contexts = [
      'section_storage' => $section_storage,
      'delta' => $delta,
      'region' => $region
    ];
    $this->moduleHandler->alter('layout_builder_browser', $build, $contexts);

    return $build;
  }

  /**
   * Gets a render array of block links.
   *
   * @param \Drupal\layout_builder\SectionStorageInterface $section_storage
   *   The section storage.
   * @param int $delta
   *   The delta of the section to splice.
   * @param string $region
   *   The region the block is going in.
   * @param array $blocks
   *   The information for each block.
   *
   * @return array
   *   The block links render array.
   */
  protected function getBlocks(SectionStorageInterface $section_storage, $delta, $region, array $blocks) {
    $links = [];

    foreach ($blocks as $block_id => $block) {
      $attributes = $this->getAjaxAttributes();
      $attributes['class'][] = 'js-layout-builder-block-link';
      $attributes['class'][] = 'layout-builder-browser-block-item';

      $block_render_array = [];
      if (!empty($block["layout_builder_browser_data"]) && isset($block["layout_builder_browser_data"]->image_path) && trim($block["layout_builder_browser_data"]->image_path) != '') {
        $block_render_array['image'] = [
          '#theme' => 'image',
          '#uri' => $block["layout_builder_browser_data"]->image_path,
          '#alt' => $block['layout_builder_browser_data']->image_alt,
        ];
      }
      $block_render_array['label'] = ['#markup' => (empty($block["layout_builder_browser_data"])) ? $block['admin_label'] : $block["layout_builder_browser_data"]->label()];
      $link = [
        '#type' => 'link',
        '#title' => $block_render_array,
        '#url' => Url::fromRoute('layout_builder.add_block',
          [
            'section_storage_type' => $section_storage->getStorageType(),
            'section_storage' => $section_storage->getStorageId(),
            'delta' => $delta,
            'region' => $region,
            'plugin_id' => $block_id,
          ]
        ),
        '#attributes' => $attributes,
      ];

      $links[] = $link;
    }
    return $links;
  }

  /**
   * Get dialog attributes if an ajax request.
   *
   * @return array
   *   The attributes array.
   */
  protected function getAjaxAttributes() {
    if ($this->isAjax()) {
      return [
        'class' => ['use-ajax'],
        'data-dialog-type' => 'dialog',
        'data-dialog-renderer' => 'off_canvas',
        'data-dialog-options' => Json::encode(['width' => '500px']),
      ];
    }
    return [];
  }

}
