<?php

namespace Drupal\surf_registration\Entity\Node;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\surf_core\DrupalDateTime;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\Entity\Node;
use Drupal\surf_dashboard\Entity\EntityUserDashboardInterface;
use Drupal\surf_dashboard\Entity\EntityWebformLinkTrait;
use Drupal\surf_dashboard\Entity\EntityUserDashboardTrait;
use Drupal\surf_registration\Entity\UserRequest;

/**
 * A bundle class for node entities.
 */
class Event extends Node implements EntityUserDashboardInterface {

  use EntityUserDashboardTrait;
  use EntityWebformLinkTrait;

  protected function getUserDashboardPageId() {
    return 'dashboard_events';
  }

  protected function getWebformId() {
    return 'event_registration';
  }

  protected function getReferenceFieldName() {
    return 'ref_event';
  }

  public function getDashboardRelationEntity(AccountInterface $user) {
    return $this->getActiveUserRequest($user);
  }

  private function checkRequirements() {
    return $this->hasField('field_registration_required')
      && $this->hasField('field_registration_period')
      && $this->get('field_registration_required')->value
      && !$this->get('field_registration_period')->isEmpty()
      && $this->checkRegistrationDates();
  }

  public function getUserRegistrationLinks(AccountInterface $user) {
    $build = [];
    if (!$this->checkRequirements()) {
      return $build;
    }
    if (!$this->checkRegistrationDates()) {
      return $build;
    }

    $build['webform'] = $this->getWebformLink()->toRenderable();
    $build['dashboard'] = [];

    $cache_metadata = new CacheableMetadata();
    $cache_metadata->addCacheContexts(['user']);
    if ($user_request = $this->getActiveUserRequest($user)) {
      $cache_metadata->addCacheableDependency($user_request);
      $build['dashboard'] = $this->getUserDashboardLink($user, $user_request)->toRenderable();
    }
    $build['#has_active_reservation'] = !empty($user_request);
    $build['webform']['#access'] = empty($user_request);
    $build['dashboard']['#access'] = !empty($user_request);

    $cache_metadata->applyTo($build);

    return $build;
  }

  private function checkRegistrationDates() {
    $now = new DrupalDateTime();
    $date_start = $this->field_registration_period->start_date;
    $date_end = $this->field_registration_period->end_date;
    $date_end->add(new \DateInterval('P1D'));
    return $now->isAfterDate($date_start) && $now->isBeforeDate($date_end);
  }

  public function getActiveUserRequest(AccountInterface $user) {
    $query = $this->entityTypeManager()->getStorage('user_request')->getQuery();
    $query->condition('type', 'event_registration')
      ->condition('state', 'registered', '!=')
      ->condition('field_ref_event.target_id', $this->id());

    $result = $query->accessCheck(FALSE)->execute();
    if (empty($result)) {
      return FALSE;
    }
    $user_request_id = reset($result);
    return UserRequest::load($user_request_id);
  }


}
