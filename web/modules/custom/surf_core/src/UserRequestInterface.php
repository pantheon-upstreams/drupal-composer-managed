<?php

namespace Drupal\surf_core;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining an user request entity type.
 */
interface UserRequestInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
