<?php

namespace Drupal\surf_curriculum\Entity\Node;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Session\AccountInterface;
use Drupal\download_request\Entity\DownloadItem;
use Drupal\download_request\Entity\DownloadRequestItem;
use Drupal\node\Entity\Node;
use Drupal\surf_dashboard\Entity\EntityWebformLinkTrait;
use Drupal\surf_dashboard\Entity\EntityUserDashboardTrait;

/**
 * A bundle class for node entities.
 */
class CurriculumModule extends Node {

  use EntityUserDashboardTrait;
  use EntityWebformLinkTrait;

  protected function getUserDashboardPageId() {
    return 'dashboard_curriculum_units';
  }

  protected function getWebformId() {
    return 'curriculum_module';
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
    if (!$download_request_item = $this->getActiveDownloadRequestItem($user)) {
      return FALSE;
    }
    return $download_request_item->getParentEntity();
  }

  public function getActiveDownloadRequestItem(AccountInterface $user) {
    $query = $this->entityTypeManager()->getStorage('download_request_item')->getQuery();
    $query->accessCheck(FALSE)
      ->condition('state', 'returned', '!=')
      ->condition('download_item.entity.type', 'curriculum_module_resources')
      ->condition('download_item.entity.field_curriculum_module.target_id', $this->id());

    $result = $query->execute();
    if (empty($result)) {
      return FALSE;
    }
    $download_request_item_id = reset($result);
    return DownloadRequestItem::load($download_request_item_id);
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
