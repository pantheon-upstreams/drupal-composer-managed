<?php

namespace Drupal\advagg_validator\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Configure form for CSSHint validation of CSS files.
 */
class CssLintForm extends BaseValidatorForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'advagg_validator_csslint';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::generateForm('css');
    $rules = [];
    if (file_exists(DRUPAL_ROOT . '/.csslintrc')) {
      $rule_string = file_get_contents(DRUPAL_ROOT . '/.csslintrc');
      if (empty($rule_string)) {
      }
      elseif (substr($rule_string, 0, 1) === '{') {
        $rules = json_decode($rule_string, TRUE);
      }
      elseif (substr($rule_string, 0, 1) === '-') {
        $rules = $this->decodeRules($rule_string);
      }
    }
    $form['#attached']['library'][] = 'advagg_validator/csslint';
    $form['#attached']['drupalSettings']['csslint'] = [
      'rules' => $rules,
    ];
    $form = parent::buildForm($form, $form_state);
    unset($form['actions']);
    return $form;
  }

  /**
   * Decode a .csslintrc file to rules array.
   *
   * @param string $rule_string
   *   The raw rule string.
   *
   * @return array
   *   The rules array.
   */
  protected function decodeRules($rule_string) {
    $values = [
      'errors' => 2,
      'warning' => 1,
      'ignore' => 0,
      'exclude-list' => 1,
    ];
    $rules = [];
    $group = '';
    $raw = explode("\n", $rule_string);
    foreach ($raw as $rule) {
      if (empty($rule)) {
        continue;
      }
      if (substr($rule, 0, 1) === '-') {
        $split = explode('=', trim($rule, " \t\n\r\0\x0B-,"));
        $group = $split[0];
        $rules[$group] = [];
        if (isset($split[1])) {
          $rules[$group][$split[1]] = $values[$group];
        }
        continue;
      }
      $rules[$group][trim($rule, " \t\n\r\0\x0B,")] = $values[$group];
    }
    return $rules;
  }

}
