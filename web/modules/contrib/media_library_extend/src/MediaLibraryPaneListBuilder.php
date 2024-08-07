<?php

namespace Drupal\media_library_extend;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a listing of Media library pane entities.
 */
class MediaLibraryPaneListBuilder extends ConfigEntityListBuilder {

  /**
   * The media type storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $mediaStorage;

  /**
   * Constructs a new ActionListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The media library pane storage.
   * @param \Drupal\Core\Entity\EntityStorageInterface $media_storage
   *   The media type storage.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, EntityStorageInterface $media_storage) {
    parent::__construct($entity_type, $storage);
    $this->mediaStorage = $media_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity_type.manager')->getStorage($entity_type->id()),
      $container->get('entity_type.manager')->getStorage('media_type'));
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Media library pane');
    $header['source_plugin'] = $this->t('Source plugin');
    $header['bundle'] = $this->t('Target bundle');
    $header['summary'] = $this->t('Summary');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    $row['source_plugin'] = $entity->getPlugin()->label();
    $row['bundle'] = $this->mediaStorage->load($entity->getTargetBundle())->label();
    $row['summary']['data'] = $entity->getSummary();
    return $row + parent::buildRow($entity);
  }

}
