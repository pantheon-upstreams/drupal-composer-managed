<?php

namespace Drupal\surf_curriculum;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining a curriculum reservation entity type.
 */
interface CurriculumReservationInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
