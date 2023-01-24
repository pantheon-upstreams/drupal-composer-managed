<?php

namespace Drupal\surf_core\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the User Request type configuration entity.
 *
 * @ConfigEntityType(
 *   id = "user_request_type",
 *   label = @Translation("User Request type"),
 *   label_collection = @Translation("User Request types"),
 *   label_singular = @Translation("user request type"),
 *   label_plural = @Translation("user requests types"),
 *   label_count = @PluralTranslation(
 *     singular = "@count user requests type",
 *     plural = "@count user requests types",
 *   ),
 *   handlers = {
 *     "form" = {
 *       "add" = "Drupal\surf_core\Form\UserRequestTypeForm",
 *       "edit" = "Drupal\surf_core\Form\UserRequestTypeForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
 *     },
 *     "list_builder" = "Drupal\surf_core\UserRequestTypeListBuilder",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   admin_permission = "administer user request types",
 *   bundle_of = "user_request",
 *   config_prefix = "user_request_type",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "add-form" = "/admin/structure/user_request_types/add",
 *     "edit-form" = "/admin/structure/user_request_types/manage/{user_request_type}",
 *     "delete-form" = "/admin/structure/user_request_types/manage/{user_request_type}/delete",
 *     "collection" = "/admin/structure/user_request_types"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "uuid",
 *   }
 * )
 */
class UserRequestType extends ConfigEntityBundleBase {

  /**
   * The machine name of this user request type.
   *
   * @var string
   */
  protected $id;

  /**
   * The human-readable name of the user request type.
   *
   * @var string
   */
  protected $label;

}
