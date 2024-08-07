<?php

namespace Drupal\imce;

/**
 * Imce Item.
 */
abstract class ImceItem {

  /**
   * Item type.
   *
   * @var string
   */
  public $type;

  /**
   * Item name.
   *
   * @var string
   */
  public $name;

  /**
   * Selected status.
   *
   * @var bool
   */
  public $selected;

  /**
   * Item parent.
   *
   * @var \Drupal\imce\ImceFolder
   */
  public $parent;

  /**
   * File manager.
   *
   * @var \Drupal\imce\ImceFM
   */
  protected $fm;

  /**
   * Item path relative to the root.
   *
   * @var string
   */
  protected $path;

  /**
   * Constructs the item.
   *
   * @param string $name
   *   Item name.
   */
  public function __construct($name) {
    $this->name = $name;
  }

  /**
   * Returns the file manager.
   */
  public function fm() {
    return $this->fm;
  }

  /**
   * Sets the file manager.
   */
  public function setFm(ImceFM $fm) {
    $this->fm = $fm;
  }

  /**
   * Returns the item path relative to the root.
   */
  public function getPath() {
    if (isset($this->path)) {
      return $this->path;
    }
    if ($this->parent) {
      $path = $this->parent->getPath();
      if (isset($path)) {
        return Imce::joinPaths($path, $this->name);
      }
    }
  }

  /**
   * Sets the item path.
   */
  public function setPath($path) {
    $this->path = $path;
  }

  /**
   * Returns the item uri.
   */
  public function getUri() {
    $path = $this->getPath();
    if (isset($path)) {
      return $this->fm()->createUri($path);
    }
  }

  /**
   * Selects the item.
   */
  public function select() {
    $this->fm()->selectItem($this);
  }

  /**
   * Deselects the item.
   */
  public function deselect() {
    $this->fm()->deselectItem($this);
  }

  /**
   * Removes the item from its parent.
   */
  public function remove() {
    if ($this->parent) {
      $this->parent->removeItem($this);
    }
  }

  /**
   * Removes the item from js.
   */
  public function removeFromJs() {
    $this->fm()->removeItemFromJs($this);
  }

  /**
   * Adds the item to js.
   */
  public function addToJs() {
    $this->fm()->addItemToJs($this);
  }

}
