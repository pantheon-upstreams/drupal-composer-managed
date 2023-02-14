<?php

namespace Drupal\surf_core;

use Drupal\Component\Utility\NestedArray;
use Drupal\node\Entity\NodeType;

trait EntityNodeThirdPartySettingsTrait {

  protected function getThirdPartySetting($module, $primary_key, $secondary_key) {
    $node_type = NodeType::load($this->bundle());
    $third_party_settings = $node_type->getThirdPartySetting($module, $primary_key);
    return NestedArray::getValue($third_party_settings, [$secondary_key]);
  }
}