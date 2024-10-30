<?php

namespace Drupal\du_widen\Plugin\views\field;

use Drupal\entity_browser\Plugin\views\field\SelectForm as EntityBrowserSelectForm;
use Drupal\views\ResultRow;

/**
 * Define a bulk operation form element that works with Widen entity browser.
 *
 * @ViewsField("widen_select")
 */
class SelectForm extends EntityBrowserSelectForm {

  /**
   * Returns the ID for a result row.
   *
   * @param \Drupal\views\ResultRow $row
   *   The result row.
   *
   * @return string
   *   The row ID, in the form ENTITY_TYPE:ENTITY_ID.
   */
  public function getRowId(ResultRow $row) {
    return $row->external_id . ':' . $row->filename . ':' . $row->width . ':' . $row->height . ':' . $row->alt_text;
  }

}
