<?php

namespace Drupal\surf_curriculum;

use Drupal\Core\Access\AccessResult;
use Drupal\entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the curriculum reservation entity type.
 */
class CurriculumReservationAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {

    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view curriculum reservation');

      case 'update':
        return AccessResult::allowedIfHasPermissions(
          $account,
          ['edit curriculum reservation', 'administer curriculum reservation'],
          'OR',
        );

      case 'delete':
        return AccessResult::allowedIfHasPermissions(
          $account,
          ['delete curriculum reservation', 'administer curriculum reservation'],
          'OR',
        );

      default:
        // No opinion.
        return AccessResult::neutral();
    }

  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermissions(
      $account,
      ['create curriculum reservation', 'administer curriculum reservation'],
      'OR',
    );
  }

}
