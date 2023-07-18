<?php

namespace Drupal\simple_menu_permissions;

use Drupal\system\Entity\Menu;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\system\MenuInterface;

/**
 * Provides dynamic permissions for different menu's.
 */
class SimpleMenuPermissionsPermissions {

  use StringTranslationTrait;

  /**
   * Returns an array of menu permissions.
   *
   * @return array
   *   The menu permissions.
   */
  public function get(): array {
    $perms = [];

    // Load the existing menus.
    $menus = Menu::loadMultiple();

    // Add the permission to create new menus.
    $perms['create new menu'] = [
      'title' => $this->t('Create new menu'),
    ];

    foreach ($menus as $menu) {
      // Add the permissions for each menu.
      $perms += $this->buildPermissions($menu);
    }

    return $perms;
  }

  /**
   * Returns an array of menu permissions.
   */
  protected function buildPermissions(MenuInterface $menu): array {
    // Define and return all permissions that are available for each menu.
    return [
      'view ' . $menu->id() . ' menu in menu list' => [
        'title' => $this->t('View %menu_name menu in the menu list', [
          '%menu_name' => $menu->label(),
        ]),
      ],
      'edit ' . $menu->id() . ' menu' => [
        'title' => $this->t('Edit %menu_name menu', [
          '%menu_name' => $menu->label(),
        ]),
      ],
      'delete ' . $menu->id() . ' menu' => [
        'title' => $this->t('Delete %menu_name menu', [
          '%menu_name' => $menu->label(),
        ]),
      ],
      'add new links to ' . $menu->id() . ' menu' => [
        'title' => $this->t('Add new links to %menu_name menu', [
          '%menu_name' => $menu->label(),
        ]),
      ],
      'edit links in ' . $menu->id() . ' menu' => [
        'title' => $this->t('Edit links in %menu_name menu', [
          '%menu_name' => $menu->label(),
        ]),
      ],
      'delete links in ' . $menu->id() . ' menu' => [
        'title' => $this->t('Delete links in %menu_name menu', [
          '%menu_name' => $menu->label(),
        ]),
      ],
    ];
  }

}
