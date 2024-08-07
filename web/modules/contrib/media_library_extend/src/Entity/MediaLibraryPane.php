<?php

namespace Drupal\media_library_extend\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Media library pane entity.
 *
 * @ConfigEntityType(
 *   id = "media_library_pane",
 *   label = @Translation("Media library pane"),
 *   label_collection = @Translation("Media library panes"),
 *   label_singular = @Translation("Media library pane"),
 *   label_plural = @Translation("Media library panes"),
 *   label_count = @PluralTranslation(
 *     singular = "@count media library pane",
 *     plural = "@count media library panes",
 *   ),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\media_library_extend\MediaLibraryPaneListBuilder",
 *     "form" = {
 *       "default" = "Drupal\media_library_extend\Form\MediaLibraryPaneForm",
 *       "delete" = "Drupal\media_library_extend\Form\MediaLibraryPaneDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "pane",
 *   config_export = {
 *     "id",
 *     "label",
 *     "uuid",
 *     "bundle",
 *     "source_plugin",
 *     "source_plugin_configuration"
 *   },
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "canonical" = "/admin/config/media/media-library/pane/{media_library_pane}",
 *     "add-form" = "/admin/config/media/media-library/pane/add",
 *     "edit-form" = "/admin/config/media/media-library/pane/{media_library_pane}/edit",
 *     "delete-form" = "/admin/config/media/media-library/pane/{media_library_pane}/delete",
 *     "collection" = "/admin/config/media/media-library/pane",
 *   }
 * )
 */
class MediaLibraryPane extends ConfigEntityBase implements MediaLibraryPaneInterface {

  /**
   * The Media library pane ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Media library pane label.
   *
   * @var string
   */
  protected $label;

  /**
   * The media bundle associated with this pane.
   *
   * @var string
   */
  protected $bundle;

  /**
   * The source plugin for this pane.
   *
   * @var string
   */
  protected $source_plugin;

  /**
   * The source plugin for this pane.
   *
   * @var array
   */
  protected $source_plugin_configuration;

  /**
   * {@inheritdoc}
   */
  public function getTargetBundle() {
    return $this->bundle;
  }

  /**
   * {@inheritdoc}
   */
  public function getSourcePluginId() {
    return $this->source_plugin;
  }

  /**
   * {@inheritdoc}
   */
  public function getSourcePluginConfiguration() {
    return $this->source_plugin_configuration ?? [];
  }

  /**
   * {@inheritdoc}
   */
  public function getPlugin() {
    // @todo Apply plugin configuration.
    $plugin = \Drupal::service('plugin.manager.media_library_source')->createInstance($this->source_plugin, $this->getSourcePluginConfiguration());
    $plugin->setTargetBundle($this->bundle);

    return $plugin;
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary() {
    return $this->getPlugin()->getSummary();
  }

}
