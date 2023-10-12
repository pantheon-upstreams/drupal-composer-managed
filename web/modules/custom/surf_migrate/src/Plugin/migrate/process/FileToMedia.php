<?php

namespace Drupal\surf_migrate\Plugin\migrate\process;

use Drupal\Core\Database\Database;
use Drupal\Core\File\FileSystemInterface;
use Drupal\file\Entity\File;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateSkipProcessException;
use Drupal\media\entity\Media;
use Drupal\surf_migrate\FileToMediaTrait;

/**
 * Will fetch and create media from external source.
 *
 * @MigrateProcessPlugin(
 *   id = "file_to_media",
 *   handle_multiples = FALSE
 * )
 */
class FileToMedia extends ProcessPluginBase {

  use FileToMediaTrait;

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $config = [
      'destination_field' => $this->configuration['destination_field'] ?? 'auto',
      'media_bundle' => $this->configuration['media_bundle'] ?? 'auto',
      'metadata' => $this->configuration['metadata'] ?? [],
    ];

    $this->processRowTokens($config['metadata'], $row);

    if (empty($value)) {
      throw new MigrateSkipProcessException();
    }
    if (!$file = File::load($value)) {
      throw new MigrateSkipProcessException();
    }

    return $this->destFidToDestMid($file->id(), $config);
  }

  private function processRowTokens(&$metadata, Row $row) {
    foreach ($metadata as &$value) {
      if (!$row->get($value)) {
        continue;
      }
      $value = $row->get($value);
    }
  }

}
