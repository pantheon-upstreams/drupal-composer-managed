<?php

/**
 * @file
 * Contains Drupal\hearsay_content_configurations\Form\HsAdminContentSettings.
 */

namespace Drupal\hearsay_content_configurations\Form;

use Drupal\hearsay_common\Controller\HearsayCommon;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class HsAdminContentSettings.
 *
 * @package Drupal\hearsay_content_configurations\Form
 */
class HsAdminContentSettings extends ConfigFormBase
{
  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames()
  {
    return [
      'hearsay_admin_content_settings.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'hearsay_admin_content_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $config = $this->config('hearsay_admin_content_settings.settings');

    $form['thrivenths_config'] = [
      '#type' => 'vertical_tabs',
      '#default_tab' => 'edit-content',
    ];

    $form['global_elements'] = [
      '#type' => 'details',
      '#title' => $this->t('Global Elements'),
      '#open' => true,
      '#group' => 'thrivenths_config',
    ];

    // Global Elements

    $form['global_elements']['404_link'] = [
      '#type' => 'textfield',
      '#title' => $this->t('404 Redirect Link'),
      '#default_value' => $config->get('404_link'),
      '#placeholder' => 'Enter 404 Redirect Link.'
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    parent::submitForm($form, $form_state);
    $config = $this->config('hearsay_admin_content_settings.settings');
    foreach ($form_state->getValues() as $id => $value) {
      $config->set($id, $value);
    }
    $config->save();
  }
}
