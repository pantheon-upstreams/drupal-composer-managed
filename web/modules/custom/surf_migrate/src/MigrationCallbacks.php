<?php

namespace Drupal\surf_migrate;

use Drupal\Core\Database\Database;

class MigrationCallbacks {

  public static function convertDocumentNodeToFid($value) {
    $source_connection = Database::getConnection('default', 'migrate');
    $result = $source_connection->select('node__field_document')
      ->fields(NULL, ['field_document_target_id'])
      ->condition('entity_id', $value, '=')
      ->execute()
      ->fetchCol();

    return $result ? reset($result) : FALSE;
  }
}