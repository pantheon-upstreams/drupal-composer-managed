<?php

namespace Drupal\section_library;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Section library template entities.
 *
 * @ingroup section_library
 */
class SectionLibraryTemplateListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Section library template ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\section_library\Entity\SectionLibraryTemplate $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.section_library_template.edit_form',
      ['section_library_template' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
