<?php

namespace Drupal\section_library;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Section library template entity.
 *
 * @see \Drupal\section_library\Entity\SectionLibraryTemplate.
 */
class SectionLibraryTemplateAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\section_library\Entity\SectionLibraryTemplateInterface $entity */

    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view section library templates');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit section library templates');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete section library templates');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add section library templates');
  }

}
