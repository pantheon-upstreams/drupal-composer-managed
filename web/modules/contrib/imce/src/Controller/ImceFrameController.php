<?php

namespace Drupal\imce\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\imce\Imce;

/**
 * Controller for /user/{user}/imce page.
 */
class ImceFrameController extends ControllerBase {

  /**
   * Browser Page.
   *
   * @return string
   *   Return the IMCE file manager in a frame.
   */
  public function page() {
    $render['iframe'] = [
      '#type' => 'inline_template',
      '#template' => '<iframe class="imce-browser" src="{{ url }}"></iframe>',
      '#context' => [
        'url' => Url::fromRoute('imce.page')->toString(),
      ],
    ];
    $render['#attached']['library'][] = 'imce/drupal.imce.admin';
    return $render;
  }

  /**
   * Checks access to /user/{user}/imce path.
   */
  public function checkAccess() {
    $path = \Drupal::service('path.current')->getPath();
    $args = explode('/', trim($path, '/'));
    $user = $this->currentUser();
    $uid = $user->id();
    $access = "$uid" === $args[1] || $user->hasPermission('administer imce');
    $profile = $access ? Imce::userProfile($user) : FALSE;
    return AccessResult::allowedIf($profile && $profile->getConf('usertab'));
  }

}
