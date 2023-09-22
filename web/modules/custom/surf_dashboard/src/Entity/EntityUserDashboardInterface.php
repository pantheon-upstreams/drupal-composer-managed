<?php

namespace Drupal\surf_dashboard\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

interface EntityUserDashboardInterface {

  public function getUserDashboardUrl(AccountInterface $user, EntityInterface $entity);

  public function getUserDashboardLink(AccountInterface $user, EntityInterface $entity, $text = NULL);

  public function getDashboardRelationEntity(AccountInterface $user);
}