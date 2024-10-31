<?php

namespace Drupal\du_site\Controller;

use Drupal\file\Entity\File;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Controller for du_site file download.
 */
class FileController {

  /**
   * Redirects user to correct file location.
   *
   * @param int $file_id
   *   File ID that we're trying to load.
   *
   * @return mixed
   *   Issues redirect is file exists, otherwise shows 404 page.
   */
  public function content($file_id) {
    $file = File::load($file_id);
    if (!empty($file)) {
      $file_uri = $file->getFileUri();
      $url = \Drupal::service('file_url_generator')->generateAbsoluteString($file_uri);

      return new RedirectResponse($url);
    }

    throw new NotFoundHttpException();
  }

}
