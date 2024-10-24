<?php

namespace Drupal\du_widen\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * WidenConfigForm class.
 */
class WidenConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'du_widen_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $config = $this->config('du_widen.settings');

    $form['key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Key'),
      '#default_value' => $config->get('key'),
      '#required' => FALSE,
    ];

    $form['url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Widen API URL'),
      '#default_value' => $config->get('url'),
      '#required' => FALSE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('du_widen.settings');
    $config->set('key', $form_state->getValue('key'));
    $config->set('url', $form_state->getValue('url'));
    $config->save();
    return parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['du_widen.settings'];
  }

}
