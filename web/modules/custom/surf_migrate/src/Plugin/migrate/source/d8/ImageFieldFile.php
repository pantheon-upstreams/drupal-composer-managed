<?php

namespace Drupal\surf_migrate\Plugin\migrate\source\d8;

use Drupal\Core\Database\Query\ConditionInterface;
use Drupal\Core\Database\Query\SelectInterface;

/**
 * Drupal 8 file source from database restricted to used files.
 *
 * @MigrateSource(
 *   id = "d8_image_field_file"
 * )
 */
class ImageFieldFile extends FileFieldFile {

  protected function getConfigKey() {
    return 'image_fields';
  }

  protected function addLeftJoin($field, SelectInterface $query, ConditionInterface $condition) {
    parent::addLeftJoin($field, $query, $condition);
    list(,$field_name) = explode(':', $field);
    $query->fields($field_name, [
      $field_name . '_' . 'alt',
      $field_name . '_' . 'title',
      $field_name . '_' . 'width',
      $field_name . '_' . 'height',
    ]);
  }
}