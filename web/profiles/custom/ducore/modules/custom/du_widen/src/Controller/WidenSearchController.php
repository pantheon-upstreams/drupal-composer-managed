<?php

namespace Drupal\du_widen\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\widencollective\WidencollectiveSearchService;

/**
 * Widen search controller for the widencollective module.
 */
class WidenSearchController extends ControllerBase {

  /**
   * Request widen search url.
   *
   * @return json
   *   Returns a JSON feed.
   */
  public function getSearchUrl() {
    $config = \Drupal::config('du_widen.settings');
    $result = WidencollectiveSearchService::getSearchConnectorUiUrl($config->get('key'));
    return new JsonResponse($result);
  }

}
