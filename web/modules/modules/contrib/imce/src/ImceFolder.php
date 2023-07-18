<?php

namespace Drupal\imce;

/**
 * Imce Folder.
 */
class ImceFolder extends ImceItem {

  /**
   * {@inheritdoc}
   */
  public $type = 'folder';

  /**
   * Inheritable folder configuration including permissions.
   *
   * @var array
   */
  public $conf;

  /**
   * Scan status.
   *
   * @var bool
   */
  public $scanned;

  /**
   * Items.
   *
   * @var array
   */
  public $items = [];

  /**
   * Files.
   *
   * @var array
   */
  public $files = [];

  /**
   * Subfolders.
   *
   * @var array
   */
  public $subfolders = [];

  /**
   * Constructs the folder.
   *
   * @param string $name
   *   Folder name.
   * @param array $conf
   *   Folder configuration.
   */
  public function __construct($name, array $conf = NULL) {
    parent::__construct($name);
    $this->setConf($conf);
  }

  /**
   * Returns folder configuration.
   */
  public function getConf() {
    if (isset($this->conf)) {
      return $this->conf;
    }
    // Inherit parent conf.
    if ($parent = $this->parent) {
      if ($conf = $parent->getConf()) {
        if (Imce::permissionInFolderConf('browse_subfolders', $conf)) {
          return $conf + ['inherited' => TRUE];
        }
      }
    }
  }

  /**
   * Sets folder configuration.
   */
  public function setConf(array $conf = NULL) {
    $this->conf = $conf;
  }

  /**
   * Returns a permission value.
   */
  public function getPermission($name) {
    return Imce::permissionInFolderConf($name, $this->getConf());
  }

  /**
   * Sets the folder path.
   */
  public function setPath($path) {
    $oldpath = $this->path;
    if ($path !== $oldpath) {
      // Remove oldpath references.
      if (isset($oldpath)) {
        unset($this->fm()->tree[$oldpath]);
        foreach ($this->subfolders as $name => $item) {
          $item->setPath(NULL);
        }
      }
      // Add new path references.
      $this->path = $path;
      if (isset($path)) {
        $this->fm()->tree[$path] = $this;
        foreach ($this->subfolders as $name => $item) {
          $item->setPath(Imce::joinPaths($path, $name));
        }
      }
    }
  }

  /**
   * Returns an item by name.
   */
  public function getItem($name) {
    if (isset($this->items[$name])) {
      $item = $this->items[$name];
      if (!is_object($item)) {
        $item = isset($this->subfolders[$name]) ? $this->addSubfolder($name) : $this->addFile($name);
      }
      return $item;
    }
  }

  /**
   * Returns an item by name.
   *
   * Scans the folder if needed.
   */
  public function checkItem($name) {
    if (!$item = $this->getItem($name)) {
      if (!$this->scanned) {
        $this->scan();
        $item = $this->getItem($name);
      }
    }
    return $item;
  }

  /**
   * Appends an item to the item list.
   */
  public function appendItem(ImceItem $item) {
    $parent = $item->parent;
    if ($item !== $this && $parent !== $this) {
      if ($parent) {
        $parent->removeItem($item);
      }
      $item->parent = $this;
      $name = $item->name;
      $this->items[$name] = $item;
      if ($item->type === 'folder') {
        $this->subfolders[$name] = $item;
        $path = ($this->parent ? $this->getPath() . '/' : '') . $name;
        $item->setPath($path);
      }
      else {
        $this->files[$name] = $item;
      }
    }
    return $item;
  }

  /**
   * Removes an item from the item list.
   */
  public function removeItem(ImceItem $item) {
    if ($this === $item->parent) {
      $item->deselect();
      $item->parent = NULL;
      $name = $item->name;
      unset($this->items[$name]);
      if ($item->type === 'folder') {
        unset($this->subfolders[$name]);
        $item->setPath(NULL);
      }
      else {
        unset($this->files[$name]);
      }
      return $item;
    }
  }

  /**
   * Creates and returns a child file/folder object by name.
   */
  public function createItem($type, $name, $conf = NULL) {
    $item = $this->fm()->createItem($type, $name, $conf);
    $this->appendItem($item);
    return $item;
  }

  /**
   * Adds a file by name.
   */
  public function addFile($name) {
    return $this->createItem('file', $name);
  }

  /**
   * Adds a subfolder by name.
   */
  public function addSubfolder($name, $conf = NULL) {
    return $this->createItem('folder', $name, $conf);
  }

  /**
   * Checks if the folder is predefined.
   */
  public function isPredefined() {
    return isset($this->conf);
  }

  /**
   * Returns the first predefined descendent including itself.
   */
  public function hasPredefinedPath() {
    if ($this->isPredefined()) {
      return $this;
    }
    foreach ($this->subfolders as $folder) {
      if ($folder = $folder->hasPredefinedPath()) {
        return $folder;
      }
    }
    return FALSE;
  }

  /**
   * Scans folder content.
   */
  public function scan() {
    if (!$this->scanned) {
      $this->scanned = TRUE;
      $options = [
        'browse_files' => $this->getPermission('browse_files'),
        'browse_subfolders' => $this->getPermission('browse_subfolders'),
      ];
      $content = $this->fm()->scanDir($this->getUri(), $options);
      // Add files as raw data. We create the objects when needed.
      $this->files = $this->items = $content['files'];
      // Create the subfolder objects.
      $subfolders = $this->subfolders;
      $this->subfolders = [];
      foreach ($content['subfolders'] as $name => $uri) {
        // Check if previously created.
        if (isset($subfolders[$name]) && is_object($subfolders[$name])) {
          $this->subfolders[$name] = $this->items[$name] = $subfolders[$name];
        }
        else {
          $this->addSubfolder($name);
        }
      }
    }
  }

}
