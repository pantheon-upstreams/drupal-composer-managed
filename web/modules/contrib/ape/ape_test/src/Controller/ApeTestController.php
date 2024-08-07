<?php

namespace Drupal\ape_test\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Controller for ape testing.
 */
class ApeTestController extends ControllerBase {

  public function content() {
    $build = [
      '#type' => 'markup',
      '#markup' => t('Arrived at your final destination.'),
    ];
    return $build;
  }

  public function redirect301() {
    return $this->redirect('ape.redirect.landing', [], [], 301);
  }

  public function redirect302() {
    return $this->redirect('ape.redirect.landing', [], [], 302);
  }
}
