<?php

namespace Drupal\surf_core\Entity;

use Drupal\entity\EntityViewsData as ContribEntityViewsData;

/**
 * Provides Views data for Download request item entities.
 */
class EntityViewsData extends ContribEntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    // Copied from \Drupal\download_request\EntityViewsData
    $data = parent::getViewsData();
    $entity_type_id = $this->entityType->id();

    $data[$entity_type_id][$entity_type_id . '_bulk_form'] = [
      'title' => $this->t('Operations bulk form'),
      'help' => $this->t('Add a form element that lets you run operations on multiple entities.'),
      'field' => [
        'id' => 'surf_entity_bulk_form',
      ],
    ];

    return $data;
  }
}
