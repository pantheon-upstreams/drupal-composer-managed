<?php

namespace Drupal\section_library\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Defines the Section library template entity.
 *
 * @ingroup section_library
 *
 * @ContentEntityType(
 *   id = "section_library_template",
 *   label = @Translation("Section library template"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\section_library\SectionLibraryTemplateListBuilder",
 *     "views_data" = "Drupal\section_library\Entity\SectionLibraryTemplateViewsData",
 *     "access" = "Drupal\section_library\SectionLibraryTemplateAccessControlHandler",
 *     "form" = {
 *       "default" = "Drupal\section_library\Form\SectionLibraryForm",
 *       "edit" = "Drupal\section_library\Form\SectionLibraryForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider"
 *     },
 *   },
 *   base_table = "section_library_template",
 *   translatable = FALSE,
 *   admin_permission = "administer section library template entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "layout_section" = "layout_section"
 *   },
 *   links = {
 *     "canonical" = "/admin/content/section-library/{section_library_template}",
 *     "collection" = "/admin/content/section-library",
 *     "edit-form" = "/admin/content/section-library/{section_library_template}/edit",
 *     "delete-form" = "/admin/content/section-library/{section_library_template}/delete"
 *   }
 * )
 */
class SectionLibraryTemplate extends ContentEntityBase implements SectionLibraryTemplateInterface {
  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Section library template entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['label'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Label'))
      ->setDescription(t('The label of the Section library template.'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['type'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Type'))
      ->setSettings([
        'allowed_values' => [
          'section' => 'Section',
          'template' => 'Template',
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'hidden',
      ]);

    $fields['entity_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Entity ID'))
      ->setDescription(t('The ID of the entity of which this template added from it.'))
      ->setRequired(TRUE);

    $fields['entity_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Entity type'))
      ->setRequired(TRUE)
      ->setDescription(t('The entity type to which this template added from it.'))
      ->setSetting('is_ascii', TRUE)
      ->setSetting('max_length', EntityTypeInterface::ID_MAX_LENGTH);

    $fields['layout_section'] = BaseFieldDefinition::create('layout_section')
      ->setLabel(t('Sections'))
      ->setDescription(t('The sections storage.'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED);

    $fields['image'] = BaseFieldDefinition::create('file')
      ->setLabel(t('Image'))
      ->setDescription(t('The section image.'))
      ->setSettings([
        'uri_scheme' => 'public',
        'file_directory' => 'credentialing_providers',
        'file_extensions' => 'gif png jpg jpeg',
      ])
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'file',
        'weight' => -3,
      ])
      ->setDisplayOptions('form', [
        'type' => 'file',
        'weight' => -1,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
