<?php

namespace Drupal\surf_migrate;

use Drupal\Core\Database\Database;
use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\media\MediaInterface;
use Drupal\migrate\MigrateLookupInterface;
use Drupal\migrate\MigrateSkipProcessException;
use GuzzleHttp\Exception\GuzzleException;

trait FileToMediaTrait {


  /**
   * The migrate lookup service.
   *
   * @var \Drupal\migrate\MigrateLookupInterface
   */
  protected $migrateLookup;

  /**
   * The media entity storage.
   *
   * @var \Drupal\Core\Entity\ContentEntityStorageInterface
   */
  protected $mediaStorage;

  /**
   * @param $source_file_id
   * @param array $config
   *   - source_db
   *   - destination_db
   *   - file_scheme
   *   - destination_base_uri
   *   - source_remote_base_url
   *
   * @return null
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  protected function sourceFidToDestFid($source_file_id, array $config = []) {
    if (empty($source_file_id)) {
      return NULL;
    }

    $sourceDb = Database::getConnection('default', $config['source_db'] ?? 'migrate');
    $destinationDb = Database::getConnection('default', $config['destination_db'] ?? 'default');

    $query = $sourceDb->select('file_managed', 'fm')->fields('fm', ['uri']);
    $query->condition('fm.fid', $source_file_id);
    $sourceUri = $query->execute()->fetchField();

    if (empty($sourceUri)) {
      return NULL;
    }

    // Check if we have already created the image in the destination.
    $filename = str_replace($config['file_scheme'], '', $sourceUri);
    $filename = basename($filename);
    \Drupal::service('file_system')->prepareDirectory($config['destination_base_uri'], FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS);
    $destinationUri = $config['destination_base_uri'] . $filename;
    $query = $destinationDb->select('file_managed', 'fm')->fields('fm', ['fid']);
    $query->condition('fm.uri', $destinationUri);
    $destinationFid = $query->execute()->fetchField();

    if (empty($destinationFid)) {
      $http = \Drupal::httpClient();
      $remoteFile = str_replace('public://', $config['source_remote_base_url'], $sourceUri);
      try {
        $result = $http->request('get', $remoteFile);
      }
      catch (GuzzleException $e) {
        throw new MigrateSkipProcessException();
      }
      $file_data = $result->getBody()->getContents();
      $file = \Drupal::service('file.repository')->writeData($file_data, $destinationUri, FileSystemInterface::EXISTS_REPLACE);

      return $file->id();
    }

    return $destinationFid;
  }

  /**
   * @param $dest_file_id
   * @param array $config
   *   - destination_field
   *   - media_bundle
   *   - metadata
   * @return int|mixed|string|null
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function destFidToDestMid($dest_file_id, array $config = []) {
    $config['metadata'] = $config['metadata'] ?? [];

    $file = File::load($dest_file_id);

    if ($config['destination_field'] === 'auto' || $config['media_bundle'] === 'auto') {
      $this->autoDetectDestinationEntityInfo($file, $config);
    }

    $db = \Drupal::database();
    $destinationTable = 'media__' . $config['destination_field'];
    $query = $db->select($destinationTable, 'mfmi')->fields('mfmi');
    $query->condition('mfmi.' . $config['destination_field'] . '_target_id', $dest_file_id);
    $media_fetch_result = $query->execute()->fetchAssoc();

    if (!empty($media_fetch_result['entity_id'])) {
      return $media_fetch_result['entity_id'];
    }

    $media = Media::create([
      'bundle' => $config['media_bundle'],
      'uid' => \Drupal::currentUser()->id(),
      $config['destination_field'] => $config['metadata'] + [
          'target_id' => $dest_file_id,
        ],
    ]);

    $media->setName($file->getFilename())
      ->setPublished(TRUE)
      ->save();

    return $media->id();
  }

  private function autoDetectDestinationEntityInfo(File $file, &$config) {
    $mime_type = $file->getMimeType();

    $image_mimetypes = [
      'image/jpeg',
      'image/png'
    ];

    if (in_array($mime_type, $image_mimetypes)) {
      $config['destination_field'] = 'field_media_image';
      $config['media_bundle'] = 'image';
    }
    else {
      $config['destination_field'] = 'field_media_file';
      $config['media_bundle'] = 'file';
    }
  }
  /**
   * Returns the UUID of the migrated media entity, if any.
   *
   * @param string|int $source_id
   *   The source if of the file.
   * @param string[] $migrations
   *   List of file and/or media migrations.
   *
   * @return string|null
   *   The UUID of the migrated media entity, or NULL if it cannot be found.
   */
  protected function getExistingMediaUuid($source_id, array $migrations): ?string {
    if ($destination_id = $this->getMigratedMediaId($source_id, $migrations)) {
      $media = $this->getMediaStorage()->load($destination_id);
      if ($media instanceof MediaInterface) {
        return $media->uuid();
      }
    }
    return NULL;
  }

  protected function sourceFidToDestMid($source_id, $config) {
    if (!$dest_fid = $this->sourceFidToDestFid($source_id, $config)) {
      return NULL;
    }
    return $this->destFidToDestMid($dest_fid, $config);
  }

  /**
   * Returns the ID of the migrated media entity, if any.
   *
   * @param string|int $source_id
   *   The source if of the file.
   * @param string[] $migrations
   *   List of file and/or media migrations.
   *
   * @return string|null
   *   The ID of the migrated media entity, or NULL if it cannot be found.
   */
  protected function getMigratedMediaId(int $source_id): ?string {
    $destination_ids_array = [];
    $migrations = $this->configuration['media_migrations'] ?? [];
    foreach ($migrations as $migration) {
      try {
        $destination_ids_array = $this->getMigrateLookup()->lookup($migration, [$source_id]);
      }
      catch (\Exception $e) {
      }

      if (!empty($destination_ids_array) && isset(reset($destination_ids_array)['mid'])) {
        break;
      }
    }

    if ($destination_ids_array) {
      return reset($destination_ids_array)['mid'] ?? NULL;
    }

    return NULL;
  }

  /**
   * Returns the migrate lookup service.
   *
   * @return \Drupal\migrate\MigrateLookupInterface
   *   The migrate lookup service.
   */
  private function getMigrateLookup() {
    if (!$this->migrateLookup instanceof MigrateLookupInterface) {
      $this->migrateLookup = \Drupal::service('migrate.lookup');
    }

    return $this->migrateLookup;
  }

  /**
   * Returns the media storage.
   *
   * @return \Drupal\Core\Entity\ContentEntityStorageInterface
   *   The media storage.
   */
  private function getMediaStorage() {
    if (!$this->mediaStorage instanceof ContentEntityStorageInterface) {
      $this->mediaStorage = \Drupal::entityTypeManager()->getStorage('media');
    }

    return $this->mediaStorage;
  }
}
