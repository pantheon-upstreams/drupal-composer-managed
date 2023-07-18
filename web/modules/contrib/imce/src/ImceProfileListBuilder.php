<?php

namespace Drupal\imce;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Defines a class to build a list of Imce Profile entities.
 *
 * @see \Drupal\imce\Entity\ImceProfile
 */
class ImceProfileListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Name');
    $header['description'] = $this->t('Description');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $imce_profile) {
    /** @var \Drupal\imce\Entity\ImceProfile $imce_profile */
    $row['label'] = $imce_profile->label();
    $row['description'] = $imce_profile->get('description');
    return $row + parent::buildRow($imce_profile);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $imce_profile) {
    $operations = parent::getDefaultOperations($imce_profile);
    $operations['duplicate'] = [
      'title' => $this->t('Duplicate'),
      'weight' => 15,
      'url' => $imce_profile->toUrl('duplicate-form'),
    ];

    return $operations;
  }

}
