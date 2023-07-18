<?php

namespace Drupal\layout_builder_browser\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the LayoutBuilderBrowserBlockCategory entity.
 *
 * @ConfigEntityType(
 *   id = "layout_builder_browser_block",
 *   label = @Translation("Layout builder browser block"),
 *   handlers = {
 *     "list_builder" =
 *   "Drupal\layout_builder_browser\Form\BlockListingForm",
 *     "form" = {
 *       "add" = "Drupal\layout_builder_browser\Form\BlockForm",
 *       "edit" = "Drupal\layout_builder_browser\Form\BlockForm",
 *       "delete" =
 *   "Drupal\layout_builder_browser\Form\BlockDeleteConfirmForm",
 *     }
 *   },
 *   config_prefix = "layout_builder_browser_block",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "block_id" = "block_id",
 *     "label" = "label",
 *     "weight" = "weight",
 *   },
 *   config_export = {
 *     "id",
 *     "block_id",
 *     "category",
 *     "label",
 *     "weight",
 *     "image_path",
 *     "image_alt",
 *   },
 *   links = {
 *     "edit-form" =
 *   "/admin/config/content/layout-builder-browser/blocks/{layout_builder_browser_block}",
 *     "delete-form" =
 *   "/admin/config/content/layout-builder-browser/blocks/{layout_builder_browser_block}/delete",
 *   }
 * )
 */
class LayoutBuilderBrowserBlock extends ConfigEntityBase {

  /**
   * ID.
   *
   * @var string
   */
  public $id;

  /**
   * Block Id.
   *
   * @var string
   */
  public $block_id;

  /**
   * Category.
   *
   * @var string
   */
  public $category;

  /**
   * Label.
   *
   * @var string
   */
  protected $label;

  /**
   * Image path.
   *
   * @var string
   */
  public $image_path;

  /**
   * Image alt.
   *
   * @var string
   */
  public $image_alt;


}
