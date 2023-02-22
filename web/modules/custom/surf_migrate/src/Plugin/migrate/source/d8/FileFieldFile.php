<?php

namespace Drupal\surf_migrate\Plugin\migrate\source\d8;

use Drupal\Core\Database\Query\ConditionInterface;
use Drupal\Core\Database\Query\SelectInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\State\StateInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate_drupal_d8\Plugin\migrate\source\d8\ContentEntity;

/**
 * Drupal 8 file source from database restricted to used files.
 *
 * @MigrateSource(
 *   id = "d8_file_field_file"
 * )
 */
class FileFieldFile extends ContentEntity {

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration, StateInterface $state, EntityTypeManagerInterface $entity_type_manager, EntityFieldManagerInterface $entity_field_manager) {
    $configuration['entity_type'] = 'file';
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration, $state, $entity_type_manager, $entity_field_manager);
  }

  protected function getConfigKey() {
    return 'file_fields';
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = parent::query();
    $image_fields = $this->configuration[$this->getConfigKey()] ?? [];
    $this->processFieldsForQuery($image_fields, $query);
    return $query;
  }

  protected function processFieldsForQuery(array $fields, $query) {
    if (count($fields) === 1) {
      $field = reset($fields);
      $this->addLeftJoin($field, $query, $query);
    }
    elseif (count($fields) > 1) {
      $or = $query->orConditionGroup();
      foreach ($fields as $field) {
        $this->addLeftJoin($field, $query, $or);
      }
      $query->condition($or);
    }
  }

  protected function addLeftJoin($field, SelectInterface $query, ConditionInterface $condition) {
    list($entity_type, $field_name) = explode(':', $field);
    $query->leftJoin($entity_type . '__' . $field_name, $field_name, "{$field_name}.{$field_name}_target_id = b.fid");
    $condition->isNotNull("{$field_name}.{$field_name}_target_id");
  }
}