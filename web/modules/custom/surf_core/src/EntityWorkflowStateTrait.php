<?php

namespace Drupal\surf_core;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Exception\UnsupportedEntityTypeDefinitionException;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;

trait EntityWorkflowStateTrait {

  /**
   * Get the workflow plugin id for the parent download_request bundle.
   */
  public static function getWorkflowId(EntityInterface $entity) {
    return static::getBundleEntity($entity)->getWorkflowId() ?? 'user_request_default';
  }

  protected static function getBundleEntity(EntityInterface $entity) {
    $bundle_entity_type = $entity->getEntityType()->getBundleEntityType();
    return \Drupal::service('entity_type.manager')->getStorage($bundle_entity_type)->load($entity->bundle());
  }

  public static function workflowStateFieldDefinitions(EntityTypeInterface $entity_type) {
    if (!method_exists(static::class, 'getWorkflowId')) {
      throw new UnsupportedEntityTypeDefinitionException('Method getWorkflowId does not exist for class ' . static::class);
    }
    return ['state' => BaseFieldDefinition::create('state')
      ->setLabel(new TranslatableMarkup('State'))
      ->setRequired(TRUE)
      ->setDescription(new TranslatableMarkup(
        'The current state of the requested item.'
      ))
      // TODO: Make this configurable
      ->setDefaultValue('new')
      ->setSetting('workflow_callback', [static::class, 'getWorkflowId'])
      ->setRevisionable(TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
    ];
  }
}