<?php

namespace Drupal\media_library_extend\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\SubformState;
use Drupal\media_library_extend\Plugin\MediaLibrarySourceManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MediaLibraryPaneForm.
 */
class MediaLibraryPaneForm extends EntityForm {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The media library source plugin manager.
   *
   * @var \Drupal\media_library_extend\Plugin\MediaLibrarySourceManager
   */
  protected $sourcePluginManager;

  /**
   * Creates a new MediaLibraryPaneForm instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\media_library_extend\Plugin\MediaLibrarySourceManager $source_plugin_manager
   *   The media library source plugin manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, MediaLibrarySourceManager $source_plugin_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->sourcePluginManager = $source_plugin_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('plugin.manager.media_library_source')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $pane = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $pane->label(),
      '#description' => $this->t("Label for the Media library pane."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $pane->id(),
      '#machine_name' => [
        'exists' => '\Drupal\media_library_extend\Entity\MediaLibraryPane::load',
      ],
      '#disabled' => !$pane->isNew(),
    ];

    // Create list of available media types.
    $media_types = $this->entityTypeManager->getStorage('media_type')->loadMultiple();
    $media_type_options = [];
    foreach ($media_types as $type) {
      $media_type_options[$type->id()] = $type->label();
    }

    $form['bundle'] = [
      '#type' => 'select',
      '#title' => $this->t('Target bundle'),
      '#description' => $this->t('Selecting items in this pane will create media entities of the chosen bundle.'),
      '#default_value' => $pane->getTargetBundle(),
      '#options' => $media_type_options,
      '#empty_option' => $this->t('- Select -'),
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::ajaxUpdateAvailablePlugins',
        'wrapper' => 'plugin-settings',
      ],
    ];

    $form['plugin_settings'] = [
      '#type' => 'container',
      '#title' => $this->t('Plugin Settings'),
      '#id' => 'plugin-settings',
    ];

    // Determine selected bundle from config, overridden by form state.
    $selected_bundle = $pane->getTargetBundle();
    if ($form_state->hasValue('bundle')) {
      $selected_bundle = $form_state->getValue('bundle');
    }

    if (!empty($selected_bundle)) {
      // Show source plugin options if available.
      $source_plugins = $this->sourcePluginManager->getApplicablePlugins([$selected_bundle]);
      $source_plugin_options = [];
      foreach ($source_plugins as $plugin) {
        $source_plugin_options[$plugin->getPluginId()] = $plugin->label();
      }

      $form['plugin_settings']['source_plugin'] = [
        '#type' => 'select',
        '#title' => $this->t('Source plugin'),
        '#description' => $this->t('Select the media source plugin that should be displayed in this pane.'),
        '#default_value' => $pane->getSourcePluginId(),
        '#options' => $source_plugin_options,
        '#empty_option' => $this->t('- Select -'),
        '#required' => TRUE,
        '#ajax' => [
          'callback' => '::ajaxUpdatePluginConfiguration',
          'wrapper' => 'source-settings',
        ],
      ];
      if (empty($source_plugin_options)) {
        $form_state->setValue('source_plugin', '');
        $form['plugin_settings']['info'] = [
          '#markup' => $this->t('There are no available source plugin types for this media bundle.'),
        ];
      }

      // Determine selected source plugin from config, overridden by form state.
      $source_id = $pane->getSourcePluginId();
      if ($form_state->hasValue('source_plugin')) {
        $source_id = $form_state->getValue('source_plugin');
      }

      $form['plugin_settings']['source_plugin_configuration'] = [
        '#type' => 'container',
        '#title' => $this->t('Source Settings'),
        '#id' => 'source-settings',
        '#tree' => TRUE,
      ];

      if (!empty($source_id)) {
        // Show source plugin configuration if available.
        $configuration = $pane->getSourcePluginConfiguration();
        $plugin = $this->sourcePluginManager->createInstance($source_id, $configuration);

        if ($plugin->isConfigurable()) {
          $subform_state = SubformState::createForSubform($form['plugin_settings']['source_plugin_configuration'], $form, $form_state);
          $form['plugin_settings']['source_plugin_configuration'] += $plugin->buildConfigurationForm($form['plugin_settings']['source_plugin_configuration'], $subform_state);
        }
      }

    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $pane = $this->entity;
    $status = $pane->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Media library pane.', [
          '%label' => $pane->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Media library pane.', [
          '%label' => $pane->label(),
        ]));
    }
    $form_state->setRedirectUrl($pane->toUrl('collection'));
  }

  /**
   * Ajax callback that updates available plugin settings.
   *
   * @param array $form
   *   The form render array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   */
  public function ajaxUpdateAvailablePlugins(array &$form, FormStateInterface $form_state, Request $request) {
    return $form['plugin_settings'];
  }

  /**
   * Ajax callback that updates available source plugin configuration.
   *
   * @param array $form
   *   The form render array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   */
  public function ajaxUpdatePluginConfiguration(array &$form, FormStateInterface $form_state, Request $request) {
    return $form['plugin_settings']['source_plugin_configuration'];
  }

}
