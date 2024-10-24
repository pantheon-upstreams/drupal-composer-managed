<?php

namespace Drupal\du_widen\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\filter\FilterPluginBase;

/**
 * Filter for Widen text fields.
 *
 * Very basic filter that only allows equals operator.
 *
 * @ViewsFilter("widen_filter_text")
 */
class WidenFilterText extends FilterPluginBase {

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->no_operator = TRUE;
  }

  /**
   * {@inheritdoc}
   */
  protected function valueForm(&$form, FormStateInterface $form_state) {
    $form['value'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Value'),
      '#size' => 30,
      '#default_value' => $this->value,
    ];
  }

}
