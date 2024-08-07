<?php

namespace Drupal\media_library_extend\Plugin;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Provides a plugin manager for media library panes.
 *
 * @see plugin_api
 */
class MediaLibrarySourceManager extends DefaultPluginManager {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a MediaLibrarySourceManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct(
      'Plugin/MediaLibrarySource',
      $namespaces,
      $module_handler,
      'Drupal\media_library_extend\Plugin\MediaLibrarySourceInterface',
      'Drupal\media_library_extend\Annotation\MediaLibrarySource'
    );
    $this->alterInfo('media_library_source_info');
    $this->setCacheBackend($cache_backend, 'media_library_source_plugins');
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Gets all plugins available for the given media bundles.
   *
   * @param array $allowed_types
   *   A list of media bundle ids that are allowed.
   *
   * @return \Drupal\media_library_extend\Plugin\MediaLibrarySourceInterface[]
   *   An array of instantiated media library pane plugins, if available.
   */
  public function getApplicablePlugins(array $allowed_types) {
    $definitions = $this->getDefinitions();
    $plugins = [];

    $media_types = $this->entityTypeManager->getStorage('media_type')->loadMultiple();
    $allowed_sources = [];
    foreach ($media_types as $type) {
      if (in_array($type->id(), $allowed_types)) {
        $allowed_sources[] = $type->getSource()->getPluginId();
      }
    }

    foreach ($definitions as $id => $definition) {
      // @todo Filter allowed media bundles by their media source plugin.
      $matched_types = array_intersect($allowed_sources, $definition['source_types']);

      if (!empty($matched_types)) {
        $plugins[$id] = $this->createInstance($id, $definition);
      }
    }

    return $plugins;
  }

}
