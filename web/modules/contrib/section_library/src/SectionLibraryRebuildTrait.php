<?php

namespace Drupal\section_library;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseDialogCommand;

/**
 * Provides AJAX responses to rebuild the Layout Builder.
 */
trait SectionLibraryRebuildTrait {

  /**
   * Close the dialog.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   An AJAX response to either rebuild the layout and close the dialog, or
   *   reload the page.
   */
  protected function closeDialog() {
    $response = new AjaxResponse();
    $response->addCommand(new CloseDialogCommand('#drupal-off-canvas'));
    return $response;
  }

}
