<?php

namespace Drupal\surf_registration\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\surf_core\EntityWorkflowStateTrait;
use Drupal\surf_registration\UserRequestInterface;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the user request entity class.
 *
 * @ContentEntityType(
 *   id = "user_request",
 *   label = @Translation("User Request"),
 *   label_singular = @Translation("user request"),
 *   label_plural = @Translation("user requests"),
 *   label_collection = @Translation("User Requests"),
 *   bundle_label = @Translation("User Request type"),
 *   label_count = @PluralTranslation(
 *     singular = "@count user requests",
 *     plural = "@count user requests",
 *   ),
 *   base_table = "user_request",
 *   admin_permission = "administer user request types",
 *   bundle_entity_type = "user_request_type",
 *   field_ui_base_route = "entity.user_request_type.edit_form",
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "type",
 *     "label" = "id",
 *     "uuid" = "uuid",
 *     "owner" = "uid",
 *   },
 *   handlers = {
 *     "list_builder" = "Drupal\surf_registration\UserRequestListBuilder",
 *     "views_data" = "Drupal\surf_core\Entity\EntityViewsData",
 *     "form" = {
 *       "add" = "Drupal\surf_registration\Form\UserRequestForm",
 *       "edit" = "Drupal\surf_registration\Form\UserRequestForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *       "delete-multiple-confirm" = "Drupal\Core\Entity\Form\DeleteMultipleForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\entity\Routing\AdminHtmlRouteProvider",
 *     },
 *     "local_action_provider" = {
 *       "collection" = "\Drupal\entity\Menu\EntityCollectionLocalActionProvider",
 *     },
 *     "local_task_provider" = {
 *       "default" = "\Drupal\entity\Menu\DefaultEntityLocalTaskProvider",
 *     },
 *     "access" = "Drupal\entity\UncacheableEntityAccessControlHandler",
 *     "permission_provider" = "Drupal\entity\UncacheableEntityPermissionProvider",
 *   },
 *   links = {
 *     "add-form" = "/user-request/add/{user_request_type}",
 *     "add-page" = "/user-request/add",
 *     "canonical" = "/user-request/{user_request}",
 *     "edit-form" = "/user-request/{user_request}/edit",
 *     "delete-form" = "/user-request/{user_request}/delete",
 *     "delete-multiple-form" = "/user-request/delete"
 *   }
 * )
 */
class UserRequest extends ContentEntityBase implements UserRequestInterface {

  use EntityChangedTrait;
  use EntityOwnerTrait;
  use EntityWorkflowStateTrait;

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);
    if (!$this->getOwnerId()) {
      // If no owner has been set explicitly, make the anonymous user the owner.
      $this->setOwnerId(0);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);
    $fields += static::ownerBaseFieldDefinitions($entity_type);
    $fields += static::workflowStateFieldDefinitions($entity_type);

    $fields['uid']
      ->setDescription(t('The requester user.'));

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setDescription(t('The time that the user request was created.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'datetime_timestamp',
        'weight' => 20,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the user request was last edited.'));

    return $fields;
  }

}
