<?php

namespace Drupal\section_library\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Section library template entities.
 *
 * @ingroup section_library
 */
interface SectionLibraryTemplateInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the Section library template creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Section library template.
   */
  public function getCreatedTime();

  /**
   * Sets the Section library template creation timestamp.
   *
   * @param int $timestamp
   *   The Section library template creation timestamp.
   *
   * @return \Drupal\section_library\Entity\SectionLibraryTemplateInterface
   *   The called Section library template entity.
   */
  public function setCreatedTime($timestamp);

}
