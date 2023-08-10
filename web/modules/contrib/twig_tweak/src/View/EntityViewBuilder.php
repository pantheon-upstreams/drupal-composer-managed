<?php

namespace Drupal\twig_tweak\View;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Entity view builder.
 */
class EntityViewBuilder {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs an EntityViewBuilder object.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Builds a render array for a given entity.
   */
  public function build(EntityInterface $entity, string $view_mode = 'full', string $langcode = NULL, bool $check_access = TRUE): array {
    $build = [];
    $access = $check_access ? $entity->access('view', NULL, TRUE) : AccessResult::allowed();
    if ($access->isAllowed()) {
      $build = $this->entityTypeManager
        ->getViewBuilder($entity->getEntityTypeId())
        ->view($entity, $view_mode, $langcode);
    }
    CacheableMetadata::createFromRenderArray($build)
      ->addCacheableDependency($access)
      ->addCacheableDependency($entity)
      ->applyTo($build);
    return $build;
  }

}
