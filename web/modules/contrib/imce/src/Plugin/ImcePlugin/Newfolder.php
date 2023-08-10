<?php

namespace Drupal\imce\Plugin\ImcePlugin;

use Drupal\imce\Imce;
use Drupal\imce\ImcePluginBase;
use Drupal\imce\ImceFM;

/**
 * Defines Imce New Folder plugin.
 *
 * @ImcePlugin(
 *   id = "newfolder",
 *   label = "New Folder",
 *   weight = -15,
 *   operations = {
 *     "newfolder" = "opNewfolder"
 *   }
 * )
 */
class Newfolder extends ImcePluginBase {

  /**
   * {@inheritdoc}
   */
  public function permissionInfo() {
    return [
      'create_subfolders' => $this->t('Create subfolders'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildPage(array &$page, ImceFM $fm) {
    if ($fm->hasPermission('create_subfolders')) {
      $page['#attached']['library'][] = 'imce/drupal.imce.newfolder';
    }
  }

  /**
   * Operation handler: newfolder.
   */
  public function opNewfolder(ImceFM $fm) {
    $folder = $fm->activeFolder;
    if (!$folder || !$folder->getPermission('create_subfolders')) {
      return;
    }
    // Create folder.
    $name = $fm->getPost('newfolder');
    if (is_string($name) && $fm->validateFileName($name)) {
      // Check existence.
      $uri = Imce::joinPaths($folder->getUri(), $name);
      if (file_exists($uri)) {
        $fm->setMessage($this->t('%filename already exists.', ['%filename' => $name]));
      }
      // Create and add to js.
      elseif (mkdir($uri, $fm->getConf('chmod_directory', 0775))) {
        $folder->addSubfolder($name)->addToJs();
      }
    }
  }

}
