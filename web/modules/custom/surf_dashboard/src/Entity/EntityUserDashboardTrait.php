<?php

namespace Drupal\surf_dashboard\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Link;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\surf_core\EntityNodeThirdPartySettingsTrait;

trait EntityUserDashboardTrait {

  use EntityNodeThirdPartySettingsTrait;

  abstract protected function getUserDashboardPageId();

  public function getUserDashboardUrl(AccountInterface $user, EntityInterface $entity) {
    $page_id = $this->getUserDashboardPageId();
    $variant_id = 'layout_builder-1';
    return Url::fromRoute("page_manager.page_view_{$page_id}_{$page_id}-$variant_id", [
      'user' => $user->id(),
      $entity->getEntityTypeId() => $entity->id(),
    ]);
  }

  public function getUserDashboardLink(AccountInterface $user, EntityInterface $entity, $text = NULL) {
    $text = $text ?? $this->getThirdPartySetting('surf_dashboard', 'request_link', 'text_dashboard');
    if (!$text) {
      \Drupal::messenger()->addError(t('Dashboard link default text missing for content type @bundle', ['@bundle' => $this->bundle()]));
    }
    return Link::fromTextAndUrl($text, $this->getUserDashboardUrl($user, $entity));
  }
}
