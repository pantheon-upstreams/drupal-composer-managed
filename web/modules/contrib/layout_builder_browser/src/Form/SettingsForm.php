<?php

namespace Drupal\layout_builder_browser\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Settings form.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * The entity_type.bundle.info service.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $entityTypeBundleInfo;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /**
     * @var \Drupal\metatag\Form\MetatagSettingsForm
     */
    $instance = parent::create($container);
    $instance->entityTypeBundleInfo = $container->get('entity_type.bundle.info');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'layout_builder_browser_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['layout_builder_browser.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $block_id = NULL) {

    $config = $this->config('layout_builder_browser.settings');

    $options = [
      'defaults' => 'Defaults<br>The layout builder mainly used by site builders, on the global entity settings.',
      'overrides' => 'Overrides<br>When a user edits a specific entity layout, this will trigger. You need to enable overrides for this on the entity view mode.',
    ];
    $form['enabled_section_storages'] = [
      '#title' => $this->t('Enable layout builder browser on'),
      '#type' => 'checkboxes',
      '#options' => $options,
      '#default_value' => $config->get('enabled_section_storages'),
    ];

    $form['use_modal'] = [
      '#title' => $this->t('Show layout builder browser in modal'),
      '#description' => $this->t('If checked, the layout builder browser will be rendered in a modal instead of using the off-canvas method.'),
      '#type' => 'checkbox',
      '#default_value' => $config->get('use_modal') ?? FALSE,
    ];

    $bundles = $this->entityTypeBundleInfo->getBundleInfo('block_content');
    $bundle_options = [];
    foreach ($bundles as $machine_name => $bundle) {
      $bundle_options[$machine_name] = $bundle['label'];
    }
    $form['auto_added_reusable_block_content_bundles'] = [
      '#title' => $this->t('Automatically add reusable content blocks by bundle'),
      '#description' => $this->t('Reusable block content entities for selected bundles will automatically be visible as a separate category per bundle. Blocks already placed in another layout builder browser category will not be listed again.'),
      '#type' => 'checkboxes',
      '#options' => $bundle_options,
      '#default_value' => $config->get('auto_added_reusable_block_content_bundles') ?? [],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $config = $this->config('layout_builder_browser.settings');
    $config->set('enabled_section_storages', array_filter($form_state->getValue('enabled_section_storages')));
    $config->set('use_modal', $form_state->getValue('use_modal'));
    $config->set('auto_added_reusable_block_content_bundles', array_filter($form_state->getValue('auto_added_reusable_block_content_bundles')));
    $config->save();

    parent::submitForm($form, $form_state);
  }

}
