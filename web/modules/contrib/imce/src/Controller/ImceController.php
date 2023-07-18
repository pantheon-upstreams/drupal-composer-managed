<?php

namespace Drupal\imce\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\imce\Imce;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller routines for imce routes.
 */
class ImceController extends ControllerBase {

  /**
   * Returns an administrative overview of Imce Profiles.
   */
  public function adminOverview(Request $request) {
    // Build the settings form first.(may redirect)
    $output['settings_form'] = $this->formBuilder()->getForm('Drupal\imce\Form\ImceSettingsForm') + ['#weight' => 10];
    // Buld profile list.
    $output['profile_list'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['imce-profile-list']],
      'title' => ['#markup' => '<h2>' . $this->t('Configuration Profiles') . '</h2>'],
      'list' => $this->entityTypeManager()->getListBuilder('imce_profile')->render(),
    ];
    return $output;
  }

  /**
   * Handles requests to /imce/{scheme} path.
   */
  public function page($scheme, Request $request) {
    return Imce::response($request, $this->currentUser(), $scheme);
  }

  /**
   * Checks access to /imce/{scheme} path.
   */
  public function checkAccess($scheme) {
    return AccessResult::allowedIf(Imce::access($this->currentUser(), $scheme));
  }

}
