<?php

namespace Drupal\section_library\Form;

use Drupal\Core\Ajax\AjaxFormHelperTrait;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\layout_builder\LayoutBuilderHighlightTrait;
use Drupal\layout_builder\LayoutTempstoreRepositoryInterface;
use Drupal\layout_builder\SectionStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\section_library\Entity\SectionLibraryTemplate;
use Drupal\section_library\DeepCloningTrait;
use Drupal\section_library\SectionLibraryRebuildTrait;
use Drupal\Component\Uuid\UuidInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Provides a form for adding a template to the library.
 *
 * @internal
 *   Form classes are internal.
 */
class AddTemplateToLibraryForm extends FormBase {

  use AjaxFormHelperTrait;
  use LayoutBuilderHighlightTrait;
  use DeepCloningTrait;
  use SectionLibraryRebuildTrait;

  /**
   * The layout tempstore repository.
   *
   * @var \Drupal\layout_builder\LayoutTempstoreRepositoryInterface
   */
  protected $layoutTempstoreRepository;

  /**
   * The section storage.
   *
   * @var \Drupal\layout_builder\SectionStorageInterface
   */
  protected $sectionStorage;

  /**
   * The field delta.
   *
   * @var int
   */
  protected $delta;

  /**
   * The UUID generator.
   *
   * @var \Drupal\Component\Uuid\UuidInterface
   */
  protected $uuidGenerator;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new AddTemplateToLibraryForm.
   *
   * @param \Drupal\layout_builder\LayoutTempstoreRepositoryInterface $layout_tempstore_repository
   *   The layout tempstore repository.
   * @param \Drupal\Component\Uuid\UuidInterface $uuid
   *   The UUID generator.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(LayoutTempstoreRepositoryInterface $layout_tempstore_repository, UuidInterface $uuid, EntityTypeManagerInterface $entity_type_manager) {
    $this->layoutTempstoreRepository = $layout_tempstore_repository;
    $this->uuidGenerator = $uuid;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('layout_builder.tempstore_repository'),
      $container->get('uuid'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'section_library_add_template_to_library';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, SectionStorageInterface $section_storage = NULL, $delta = NULL) {
    $this->sectionStorage = $section_storage;
    $this->delta = $delta;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => '',
      '#required' => TRUE,
    ];

    $form['image'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Image'),
      '#description' => $this->t("Upload the section image or screenshot. <br />Allowed extensions: gif png jpg jpeg."),
      '#required' => FALSE,
      '#multiple' => FALSE,
      '#upload_location' => 'public://',
      '#upload_validators' => [
        'file_validate_extensions' => ['gif png jpg jpeg'],
      ],
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add template'),
      '#button_type' => 'primary',
    ];
    if ($this->isAjax()) {
      $form['actions']['submit']['#ajax']['callback'] = '::ajaxSubmit';
    }

    $form['#attributes']['data-layout-builder-target-highlight-id'] = $this->sectionAddHighlightId($delta);

    // Mark this as an administrative page for JavaScript ("Back to site" link).
    $form['#attached']['drupalSettings']['path']['currentPathIsAdmin'] = TRUE;
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $sections = $this->sectionStorage->getSections();
    // Deep cloning sections.
    $deep_cloned_sections = $this->deepCloneSections($sections);
    $layout_entity = $this->sectionStorage->getContextValue('entity');

    $entity_values = [
      'label' => $form_state->getValue('label'),
      'layout_section' => $deep_cloned_sections,
      'type' => 'template',
      'entity_type' => $layout_entity->getEntityTypeId(),
      'entity_id' => $layout_entity->id(),
    ];

    $fid = $form_state->getValue(['image', 0]);

    if (!empty($fid)) {
      $file = $this->entityTypeManager->getStorage('file')->load($fid);
      $file->setPermanent();
      $file->save();

      $entity_values['image'] = $fid;
    }

    $section = SectionLibraryTemplate::create($entity_values);
    $section->save();

    $this->layoutTempstoreRepository->set($this->sectionStorage);
    $form_state->setRedirectUrl($this->sectionStorage->getLayoutBuilderUrl());
  }

  /**
   * {@inheritdoc}
   */
  protected function successfulAjaxSubmit(array $form, FormStateInterface $form_state) {
    return $this->closeDialog();
  }

}
