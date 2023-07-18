<?php

namespace Drupal\bootstrap\Plugin\Preprocess;

use Drupal\bootstrap\Utility\Element;
use Drupal\bootstrap\Utility\Variables;

/**
 * Pre-processes variables for the "field_multiple_value_form" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @BootstrapPreprocess("field_multiple_value_form")
 */
class FieldMultipleValueForm extends PreprocessBase implements PreprocessInterface {

  /**
   * {@inheritdoc}
   */
  public function preprocessElement(Element $element, Variables $variables) {
    // Wrap header columns in label element for Bootstrap.
    if ($variables['multiple'] && !empty($variables['table']['#header'])) {
      $prefixes = [
        '#prefix' => '<label class="label">',
        '#suffix' => '</label>',
      ];

      foreach ($variables['table']['#header'] as &$header_row) {
        if (is_array($header_row) && isset($header_row['data'])) {
          $header_row['data'] = is_array($header_row['data']) ? ($prefixes + $header_row['data']) : ($prefixes + ['#markup' => $header_row['data']]);
        } else {
          $header_row = ['data' => $prefixes + ['#markup' => $header_row]];
        }
      }
    }
  }

}
