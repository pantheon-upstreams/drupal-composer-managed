<?php

namespace Drupal\advagg_validator\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Configure form for JsHint validation of JavaScript files.
 */
class JsHintForm extends BaseValidatorForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'advagg_validator_jshint';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::generateForm('js');
    $form['#attached']['library'][] = 'advagg_validator/jshint';
    $ignore_list = $this->config('advagg_validator.settings')->get('jshint_ignore');
    if (is_array($ignore_list)) {
      $ignore_list = implode(',', $ignore_list);
    }
    $form['#attached']['drupalSettings']['jshint'] = [
      'browser' => TRUE,
      'curly' => TRUE,
      'eqeqeq' => TRUE,
      'forin' => TRUE,
      'latedef' => TRUE,
      'newcap' => TRUE,
      'noarg' => TRUE,
      'strict' => TRUE,
      'trailing' => TRUE,
      'undef' => TRUE,
      'unused' => TRUE,
      'predef' => [
        'Drupal' => FALSE,
        'drupalSettings' => FALSE,
        'domready' => FALSE,
        'jQuery' => FALSE,
        '_' => FALSE,
        'matchMedia' => FALSE,
        'Backbone' => FALSE,
        'Modernizr' => FALSE,
        'VIE' => FALSE,
        'CKEDITOR' => FALSE,
      ],
      'ignore' => $ignore_list,
    ];
    $form = parent::buildForm($form, $form_state);
    unset($form['actions']);
    return $form;
  }

}
