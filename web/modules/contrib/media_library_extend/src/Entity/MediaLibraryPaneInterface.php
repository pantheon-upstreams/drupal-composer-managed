<?php

namespace Drupal\media_library_extend\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Media library pane entities.
 */
interface MediaLibraryPaneInterface extends ConfigEntityInterface {

  /**
   * Gets the bundle id for media items created through this pane.
   *
   * @return string
   *   The media bundle id.
   */
  public function getTargetBundle();

  /**
   * Gets the media library source plugin id for this pane.
   *
   * @return string
   *   The media library source plugin id.
   */
  public function getSourcePluginId();

  /**
   * Gets the media library source plugin configuration.
   *
   * @return array
   *   The media library source plugin configuration.
   */
  public function getSourcePluginConfiguration();

  /**
   * Gets the media library source plugin for this pane.
   *
   * @return \Drupal\media_library_extend\Plugin\MediaLibrarySourceInterface
   *   The media library source plugin id.
   */
  public function getPlugin();

  /**
   * Returns a render array summarizing the configuration of the pane.
   *
   * @return array
   *   A render array.
   */
  public function getSummary();

}
