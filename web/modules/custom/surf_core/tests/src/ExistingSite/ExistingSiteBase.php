<?php

namespace Drupal\Tests\surf_core\ExistingSite;

use Drupal\apitools\Testing\ExistingSiteBase as ApitoolsExistingSiteBase;

class ExistingSiteBase extends ApitoolsExistingSiteBase {

  // Taken from paragraphsLastEntityQueryTrait.
  protected function getLastEntityOfType($entity_type_id, $bundle = NULL, $bundle_key = 'type') {
    $query = \Drupal::entityQuery($entity_type_id)
      ->accessCheck(FALSE)
      ->sort('created', 'DESC')
      ->range(0, 1);

    if ($bundle) {
      $query->condition($bundle_key, $bundle, '=');
    }

    $query_result = $query->execute();
    $entity_id = reset($query_result);
    if (empty($entity_id)) {
      $this->fail('Could not find latest entity of type: ' . $entity_type_id);
    }
    return \Drupal::entityTypeManager()->getStorage($entity_type_id)
      ->loadUnchanged($entity_id);
  }
}