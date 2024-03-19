<?php

namespace Drupal\surf_nodes\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Field formatter for Spaceless fields.
 *
 * @FieldFormatter(
 *   id = "surf_nodes_spaceless",
 *   label = @Translation("Spaceless"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class surfNodesSpaceless extends FormatterBase {
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      // Render each element as markup.
      $elements[$delta] = ['#markup' => $item->value];
    }

    return $elements;
  }
}
