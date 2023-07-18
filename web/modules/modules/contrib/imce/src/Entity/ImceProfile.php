<?php

namespace Drupal\imce\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Imce Profile entity.
 *
 * @ConfigEntityType(
 *   id = "imce_profile",
 *   label = @Translation("Imce Profile"),
 *   handlers = {
 *     "list_builder" = "Drupal\imce\ImceProfileListBuilder",
 *     "form" = {
 *       "add" = "Drupal\imce\Form\ImceProfileForm",
 *       "edit" = "Drupal\imce\Form\ImceProfileForm",
 *       "delete" = "Drupal\imce\Form\ImceProfileDeleteForm",
 *       "duplicate" = "Drupal\imce\Form\ImceProfileForm"
 *     }
 *   },
 *   admin_permission = "administer imce",
 *   config_prefix = "profile",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "edit-form" = "/admin/config/media/imce/{imce_profile}",
 *     "delete-form" = "/admin/config/media/imce/{imce_profile}/delete",
 *     "duplicate-form" = "/admin/config/media/imce/{imce_profile}/duplicate"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *     "conf"
 *   }
 * )
 */
class ImceProfile extends ConfigEntityBase {

  /**
   * Profile ID.
   *
   * @var string
   */
  protected $id;

  /**
   * Label.
   *
   * @var string
   */
  protected $label;

  /**
   * Description.
   *
   * @var string
   */
  protected $description;

  /**
   * Configuration options.
   *
   * @var array
   */
  protected $conf = [];

  /**
   * Returns configuration options.
   */
  public function getConf($key = NULL, $default = NULL) {
    $conf = $this->conf;
    if (isset($key)) {
      return isset($conf[$key]) ? $conf[$key] : $default;
    }
    return $conf;
  }

}
