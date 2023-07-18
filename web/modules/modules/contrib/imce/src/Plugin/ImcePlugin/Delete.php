<?php

namespace Drupal\imce\Plugin\ImcePlugin;

use Drupal\imce\Imce;
use Drupal\imce\ImcePluginBase;
use Drupal\imce\ImceFM;

/**
 * Defines Imce Delete plugin.
 *
 * @ImcePlugin(
 *   id = "delete",
 *   label = "Delete",
 *   weight = -5,
 *   operations = {
 *     "delete" = "opDelete"
 *   }
 * )
 */
class Delete extends ImcePluginBase {

  /**
   * {@inheritdoc}
   */
  public function permissionInfo() {
    return [
      'delete_files' => $this->t('Delete files'),
      'delete_subfolders' => $this->t('Delete subfolders'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildPage(array &$page, ImceFM $fm) {
    // Check if delete permission exists.
    if ($fm->hasPermission('delete_files') || $fm->hasPermission('delete_subfolders')) {
      $page['#attached']['library'][] = 'imce/drupal.imce.delete';
    }
  }

  /**
   * Operation handler: delete.
   */
  public function opDelete(ImceFM $fm) {
    $items = $fm->getSelection();
    if ($this->validateDelete($fm, $items)) {
      $this->deleteItems($fm, $items);
    }
  }

  /**
   * Validates the deletion of the given items.
   */
  public function validateDelete(ImceFM $fm, array $items) {
    return $items && $fm->validatePermissions($items, 'delete_files', 'delete_subfolders') && $fm->validatePredefinedPath($items);
  }

  /**
   * Deletes a list of imce items and returns succeeded ones.
   */
  public function deleteItems(ImceFM $fm, array $items) {
    $success = [];
    $ignore_usage = $fm->getConf('ignore_usage', FALSE);
    foreach ($items as $item) {
      if ($uri = $item->getUri()) {
        $result = $item->type === 'folder' ? $this->deleteFolderUri($uri, $ignore_usage, !$item->getPermission('delete_files')) : $this->deleteFileUri($uri, $ignore_usage);
        if ($result) {
          $item->removeFromJs();
          $item->remove();
          $success[] = $item;
        }
      }
    }
    return $success;
  }

  /**
   * Deletes a file by uri.
   */
  public static function deleteFileUri($uri, $ignore_usage = FALSE) {
    // Managed file.
    if ($file = Imce::getFileEntity($uri)) {
      if (!$ignore_usage && $usage = \Drupal::service('file.usage')->listUsage($file)) {
        unset($usage['imce']);
        if ($usage) {
          \Drupal::messenger()
            ->addMessage(t('%filename is in use by another application.', ['%filename' => $file->getFilename()]), 'error');
          return FALSE;
        }
      }
      $file->delete();
      return TRUE;
    }
    // Unmanaged file.
    return \Drupal::service('file_system')->delete($uri);
  }

  /**
   * Deletes a folder by uri.
   */
  public static function deleteFolderUri($uri, $ignore_usage = FALSE, $check_files = FALSE) {
    // Get folder content without any filtering.
    $content = Imce::scanDir($uri);
    if (!empty($content['error'])) {
      return FALSE;
    }
    if ($check_files && !empty($content['files'])) {
      \Drupal::messenger()
        ->addMessage(t('%folder contains files and can not be deleted.', ['%folder' => \Drupal::service('file_system')->basename($uri)]), 'error');
      return FALSE;
    }
    // Delete subfolders first.
    foreach ($content['subfolders'] as $path) {
      if (!static::deleteFolderUri($path, $ignore_usage, $check_files)) {
        return FALSE;
      }
    }
    // Delete files.
    foreach ($content['files'] as $path) {
      if (!static::deleteFileUri($path, $ignore_usage)) {
        return FALSE;
      }
    }
    // Recently emptied folders need some refreshing
    // before the removal on windows.
    if (strncasecmp(PHP_OS, 'WIN', 3) == 0) {
      @closedir(@opendir($uri));
    }
    // Remove the folder.
    return rmdir($uri);
  }

}
