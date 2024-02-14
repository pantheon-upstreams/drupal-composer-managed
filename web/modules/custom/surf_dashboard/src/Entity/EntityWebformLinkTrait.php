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

  protected function getWebformUrlParams() {
    $ref_field_name = $this->getReferenceFieldName();
    return [$ref_field_name => $this->id()];
  }

  public function getWebformUrl($destination = NULL) {
    $params = $this->getWebformUrlParams() + [
      'webform' => $this->getWebformId(),
      'destination' => $destination ?? $this->getRedirectDestination()->get(),
    ];

    return Url::fromRoute('entity.webform.canonical', $params);
  }

  public function getWebformLink($text = NULL) {
    $text = $text ?? $this->getThirdPartySetting('surf_dashboard', 'request_link', 'text_webform');
    if (!$text) {
      \Drupal::messenger()->addError(t('Webform link default text missing for content type @bundle', ['@bundle' => $this->bundle()]));
    }
    return Link::fromTextAndUrl($text, $this->getWebformUrl());
  }
}
