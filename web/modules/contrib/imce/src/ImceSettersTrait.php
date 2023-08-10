<?php

namespace Drupal\imce;

use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StreamWrapper\StreamWrapperManagerInterface;

/**
 * The trait setters Imce.
 */
trait ImceSettersTrait {

  /**
   * Manages entity type plugin definitions.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The system file config.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $configSystemFile;

  /**
   * Provides a StreamWrapper manager.
   *
   * @var \Drupal\Core\StreamWrapper\StreamWrapperManagerInterface
   */
  protected $streamWrapperManager;

  /**
   * Set entity type manager.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  protected function setEntityTypeManager(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
    return $this;
  }

  /**
   * Set config system file.
   *
   * @param \Drupal\Core\Config\ImmutableConfig $configSystemFile
   *   The config system file.
   */
  protected function setConfigSystemFile(ImmutableConfig $configSystemFile) {
    $this->configSystemFile = $configSystemFile;
    return $this;
  }

  /**
   * Set the stream wrapper manager.
   *
   * @param \Drupal\Core\StreamWrapper\StreamWrapperManagerInterface $streamWrapperManager
   *   The stream wrapper manager.
   */
  protected function setStreamWrapperManager(StreamWrapperManagerInterface $streamWrapperManager) {
    $this->streamWrapperManager = $streamWrapperManager;
    return $this;
  }

}
