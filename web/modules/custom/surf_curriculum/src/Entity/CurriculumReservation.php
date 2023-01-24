<?php

namespace Drupal\surf_curriculum\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\surf_curriculum\CurriculumReservationInterface;
use Drupal\user\EntityOwnerTrait;

/**
 * Defines the curriculum reservation entity class.
 *
 * @ContentEntityType(
 *   id = "curriculum_reservation",
 *   label = @Translation("curriculum reservation"),
 *   label_collection = @Translation("curriculum reservations"),
 *   label_singular = @Translation("curriculum reservation"),
 *   label_plural = @Translation("curriculum reservations"),
 *   label_count = @PluralTranslation(
 *     singular = "@count curriculum reservations",
 *     plural = "@count curriculum reservations",
 *   ),
 *   handlers = {
 *     "access" = "Drupal\surf_curriculum\CurriculumReservationAccessControlHandler",
 *     "permission_provider" = "\Drupal\entity\EntityPermissionProvider",
 *     "form" = {
 *       "add" = "Drupal\surf_curriculum\Form\CurriculumReservationForm",
 *       "edit" = "Drupal\surf_curriculum\Form\CurriculumReservationForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\entity\Routing\AdminHtmlRouteProvider",
 *       "delete-multiple" = "\Drupal\entity\Routing\DeleteMultipleRouteProvider",
 *     },
 *     "local_action_provider" = {
 *       "collection" = "\Drupal\entity\Menu\EntityCollectionLocalActionProvider",
 *     },
 *     "local_task_provider" = {
 *       "default" = "\Drupal\entity\Menu\DefaultEntityLocalTaskProvider",
 *     },
 *     "list_builder" = "\Drupal\entity\BulkFormEntityListBuilder",
 *     "views_data" = "\Drupal\entity\EntityViewsData",
 *   },
 *   base_table = "curriculum_reservation",
 *   admin_permission = "administer curriculum reservation",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "owner" = "uid",
 *   },
 *   links = {
 *     "add-form" = "/curriculum-reservation/add",
 *     "edit-form" = "/curriculum-reservation/{curriculum_reservation}/edit",
 *     "canonical" = "/curriculum-reservation/{curriculum_reservation}",
 *     "collection" = "/admin/content/curriculum-reservation",
 *     "delete-form" = "/curriculum-reservation/{curriculum_reservation}/delete",
 *     "delete-multiple-form" = "/curriculum-reservation/delete",
 *   },
 *   field_ui_base_route = "entity.curriculum_reservation.settings",
 * )
 */
class CurriculumReservation extends ContentEntityBase implements CurriculumReservationInterface {

  use EntityChangedTrait;
  use EntityOwnerTrait;

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

    $fields['label'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Label'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Author'))
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback(static::class . '::getDefaultEntityOwner')
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ],
        'weight' => 15,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'author',
        'weight' => 15,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Authored on'))
      ->setDescription(t('The time that the curriculum reservation was created.'))
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
      ->setDescription(t('The time that the curriculum reservation was last edited.'));

    return $fields;
  }

}
