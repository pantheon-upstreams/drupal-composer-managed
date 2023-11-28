<?php

namespace Drupal\surf_migrate\Plugin\migrate\process;

use Drupal\Core\File\Exception\InvalidStreamWrapperException;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StreamWrapper\StreamWrapperManagerInterface;
use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @MigrateProcessPlugin(
 *   id = "prepare_directory"
 * )
 */
class PrepareDirectory extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  protected $fileSystem;

  protected $streamWrapperManager;

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('file_system'),
      $container->get('stream_wrapper_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, FileSystemInterface $file_system, StreamWrapperManagerInterface $stream_wrapper_manager) {
    $this->fileSystem = $file_system;
    $this->streamWrapperManager = $stream_wrapper_manager;
    if (!isset($configuration['directory'])) {
      throw new \InvalidArgumentException('The "directory" must be set.');
    }
    if (!$this->streamWrapperManager->isValidUri($configuration['directory'])) {
      throw new InvalidStreamWrapperException("Invalid stream wrapper: {$configuration['directory']}");
    }
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!$this->fileSystem->prepareDirectory($this->configuration['directory'], FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS)) {
      throw new MigrateException(sprintf("Error preparing directory %s", $this->configuration['directory']));
    }
    return $value;
  }

}
