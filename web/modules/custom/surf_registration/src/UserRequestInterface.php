<?php

namespace Drupal\surf_registration;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface defining an user request entity type.
 */
interface UserRequestInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
