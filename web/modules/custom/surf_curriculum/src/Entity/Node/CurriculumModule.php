<?php

namespace Drupal\surf_curriculum\Entity\Node;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Session\AccountInterface;
use Drupal\download_request\Entity\DownloadItem;
use Drupal\download_request\Entity\DownloadRequest;
use Drupal\download_request\Entity\DownloadRequestItem;
use Drupal\node\Entity\Node;
use Drupal\surf_dashboard\Entity\EntityUserDashboardInterface;
use Drupal\surf_dashboard\Entity\EntityWebformLinkTrait;
use Drupal\surf_dashboard\Entity\EntityUserDashboardTrait;

/**
 * A bundle class for node entities.
 */
class CurriculumModule extends Node implements EntityUserDashboardInterface {

  use EntityUserDashboardTrait;
  use EntityWebformLinkTrait {
    getWebformUrlParams as traitGetWebformUrlParams;
  }

  protected function getUserDashboardPageId() {
    return 'dashboard_curriculum_units';
  }

  protected function getWebformUrlParams() {
    $params = $this->traitGetWebformUrlParams();
    if ($type = $this->getResourceType()) {
      $params['ref_educator_resource_type'] = $type->id();
    }
    return $params;
  }

  private function getResourceType() {
    return $this->field_ref_educator_resource_type->entity;
  }

  protected function getWebformId() {
    $webform_id = 'curriculum_reservation';
    if (!$type = $this->getResourceType()) {
      return $webform_id;
    }
    if (!$webform = $type->field_webform->entity) {
      return $webform_id;
    }
    return $webform->id();
  }

  protected function getReferenceFieldName() {
    return 'ref_curriculum_module';
  }

  public function getDashboardRelationEntity(AccountInterface $user) {
    return $this->getActiveDownloadRequest($user);
  }

  /**
   * {@inherit}
   */
  public function save() {
    $return = parent::save();
    $this->updateDownloadItem();
    return $return;
  }

  public function getActiveDownloadRequest(AccountInterface $user) {
    $query = $this->entityTypeManager()->getStorage('download_request')->getQuery();
    $current_date = \Drupal::service('date.formatter')->format(\Drupal::time()->getCurrentTime(), 'custom', 'Y-m-d');
    $query->condition('field_dates.end_value', $current_date, '>=')
      ->condition('request_items.entity.download_item.entity.type', 'curriculum_module_resources')
      ->condition('request_items.entity.download_item.entity.field_curriculum_module.target_id', $this->id());

    $result = $query->accessCheck(FALSE)->execute();
    if (empty($result)) {
      return FALSE;
    }
    $download_request_id = reset($result);
    return DownloadRequest::load($download_request_id);
  }

  public function getUserReservationLinks(AccountInterface $user) {
    $build = [];
    $build['webform'] = $this->getWebformLink()->toRenderable();
    $build['dashboard'] = [];



    $cache_metadata = new CacheableMetadata();
    $cache_metadata->addCacheContexts(['user']);
    if ($download_request = $this->getActiveDownloadRequest($user)) {
      $cache_metadata->addCacheableDependency($download_request);
      $build['dashboard'] = $this->getUserDashboardLink($user, $download_request)->toRenderable();
    }
    $build['#has_active_reservation'] = !empty($download_request);
    $build['webform']['#access'] = empty($download_request);
    $build['dashboard']['#access'] = !empty($download_request);

    $cache_metadata->applyTo($build);

    return $build;
  }

  public function createDownloadRequestItem() {
    $request_item = DownloadRequestItem::create([
      'parent_entity_bundle' => 'curriculum_module',
      'download_item' => $this->ensureDownloadItem(),
    ]);
    return $request_item;
  }

  public function getDownloadItem() {
    $download_item_storage = $this->entityTypeManager()->getStorage('download_item');
    $result = $download_item_storage->loadByProperties(['field_curriculum_module' => $this->id()]);
    return !empty($result) ? reset($result) : NULL;
  }

  public function createDownloadItem() {
    return DownloadItem::create([
      'type' => 'curriculum_module_resources',
      'field_curriculum_module' => $this->id()
    ]);
  }

  public function ensureDownloadItem() {
    return $this->getDownloadItem() ?? $this->createDownloadItem();
  }

  public function updateDownloadItem() {
    $download_item = $this->ensureDownloadItem();
    $download_item->set('name', $this->label());
    $download_item->save();
  }

}
