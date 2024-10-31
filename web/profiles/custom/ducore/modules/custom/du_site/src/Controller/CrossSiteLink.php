<?php

namespace Drupal\du_site\Controller;

use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller for du_site node redirect.
 */
class CrossSiteLink {

  /**
   * Redirects user to correct node location.
   *
   * @param string $uuid
   *   Node UUID shared through content hub.
   *
   * @return mixed
   *   Issues redirect if node exists, otherwise shows 404 page.
   */
  public function content($uuid) {
    $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['uuid' => $uuid]);
    if (!empty($nodes)) {
      $node = reset($nodes);
      $url = Url::fromRoute('entity.node.canonical', ['node' => $node->id()]);

      return new RedirectResponse($url->toString());
    }

    throw new NotFoundHttpException();
  }

}
