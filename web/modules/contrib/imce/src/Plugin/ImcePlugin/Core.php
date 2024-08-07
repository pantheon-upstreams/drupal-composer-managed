<?php

namespace Drupal\imce\Plugin\ImcePlugin;

use Drupal\imce\ImcePluginBase;
use Drupal\imce\ImceFM;

/**
 * Defines Imce Core plugin.
 *
 * @ImcePlugin(
 *   id = "core",
 *   label = "Core",
 *   weight = -99,
 *   operations = {
 *     "browse" = "opBrowse",
 *     "uuid" = "opUuid"
 *   }
 * )
 */
class Core extends ImcePluginBase {

  /**
   * {@inheritdoc}
   */
  public function permissionInfo() {
    return [
      'browse_files' => $this->t('Browse files'),
      'browse_subfolders' => $this->t('Browse subfolders'),
    ];
  }

  /**
   * Operation handler: browse.
   */
  public function opBrowse(ImceFM $fm) {
    if ($folder = $fm->activeFolder) {
      $folder->scan();
      $uri = $folder->getUri();
      $uri_prefix = substr($uri, -1) === '/' ? $uri : $uri . '/';
      $content = ['props' => $fm->getFolderProperties($uri)];
      if ($folder->getPermission('browse_files')) {
        foreach ($folder->files as $name => $file) {
          $content['files'][$name] = $fm->getFileProperties($uri_prefix . $name);
        }
      }
      if ($folder->getPermission('browse_subfolders')) {
        foreach ($folder->subfolders as $name => $subfolder) {
          $content['subfolders'][$name] = $fm->getFolderProperties($uri_prefix . $name);
        }
      }
      $fm->addResponse('content', $content);
    }
  }

  /**
   * Operation handler: uuid.
   */
  public function opUuid(ImceFM $fm) {
    $items = $fm->getSelection();
    if (!$items || !$fm->validatePermissions($items, 'browse_files')) {
      return;
    }
    $uris = [];
    foreach ($items as $item) {
      if ($uri = $item->getUri()) {
        $uris[$uri] = $item;
      }
    }
    if ($uris) {
      /** @var \Drupal\file\FileInterface[] */
      $files = \Drupal::entityTypeManager()->getStorage('file')->loadByProperties(['uri' => array_keys($uris)]);
      $uuids = [];
      foreach ($files as $file) {
        $item = $uris[$file->getFileUri()];
        $item->uuid = $file->uuid();
        $uuids[$item->getPath()] = $item->uuid;
      }
      $fm->addResponse('uuids', $uuids);
    }
  }

}
