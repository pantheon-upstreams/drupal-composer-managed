<?php

namespace Drupal\surf_dashboard\Entity;

use Drupal\Core\Link;
use Drupal\Core\Routing\RedirectDestinationTrait;
use Drupal\Core\Url;
use Drupal\surf_core\EntityNodeThirdPartySettingsTrait;

trait EntityWebformLinkTrait {

  use EntityNodeThirdPartySettingsTrait;
  use RedirectDestinationTrait;

  abstract protected function getWebformId();

  abstract protected function getReferenceFieldName();

  public function getWebformUrl($destination = NULL) {
    $ref_field_name = $this->getReferenceFieldName();
    return Url::fromRoute('entity.webform.canonical', [
      'webform' => $this->getWebformId(),
      $ref_field_name => $this->id(),
      'destination' => $destination ?? $this->getRedirectDestination()->get(),
    ]);
  }

  public function getWebformLink($text = NULL) {
    $text = $text ?? $this->getThirdPartySetting('surf_dashboard', 'request_link', 'text_webform');
    if (!$text) {
      \Drupal::messenger()->addError(t('Webform link default text missing for content type @bundle', ['@bundle' => $this->bundle()]));
    }
    return Link::fromTextAndUrl($text, $this->getWebformUrl());
  }
}
