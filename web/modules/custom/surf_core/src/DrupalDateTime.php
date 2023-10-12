<?php

namespace Drupal\surf_core;

use Drupal\Core\Datetime\DrupalDateTime as CoreDrupalDateTime;

class DrupalDateTime extends CoreDrupalDateTime {

  public function isBeforeDate(CoreDrupalDateTime $date) {
    $diff = $this->diff($date);
    return $diff->invert === 0;
  }

  public function isAfterDate(CoreDrupalDateTime $date) {
    $diff = $this->diff($date);
    return $diff->invert === 1;
  }
}