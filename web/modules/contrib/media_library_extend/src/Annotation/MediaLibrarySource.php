<?php

namespace Drupal\media_library_extend\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a MediaLibrarySource annotation object.
 *
 * Additional annotation keys for media library panes can be defined in
 * hook_media_library_source_info_alter().
 *
 * @see \Drupal\media_library_extend\MediaLibrarySourcePaneManager
 * @see \Drupal\media_library_extend\MediaLibrarySourcePaneInterface
 *
 * @Annotation
 */
class MediaLibrarySource extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the media library pane.
   *
   * @var \Drupal\Core\Annotation\Translation
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * The name of the media library pane class.
   *
   * This is not provided manually, it will be added by the discovery mechanism.
   *
   * @var string
   */
  public $class;

  /**
   * An array of media source types the media library pane supports.
   *
   * @var array
   */
  public $source_types = [];

}
