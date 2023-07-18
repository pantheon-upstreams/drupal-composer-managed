<?php

namespace Drupal\user_redirect;

use Drupal\Component\Utility\SortArray;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Drupal\Component\Utility\UrlHelper;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Routing\TrustedRedirectResponse;

/**
 * Login And Logout Redirect Per Role helper service.
 */
class UserRedirect implements UserRedirectInterface {
  /**
   * The currently active request object.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $currentRequest;

  /**
   * The users_target.settings config object.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * The current active user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a new Login And Logout Redirect Per Role service object.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current active user.
   */
  public function __construct(RequestStack $request_stack, ConfigFactoryInterface $config_factory, AccountProxyInterface $current_user) {
    $this->currentRequest = $request_stack->getCurrentRequest();
    $this->config = $config_factory->get('user_redirect.settings');
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public function setLoginRedirection(AccountInterface $account = NULL) {
    $this->prepareDestination(UserRedirectInterface::KEY_LOGIN, $account);
  }

  /**
   * {@inheritdoc}
   */
  public function setLogoutRedirection(AccountInterface $account = NULL) {
    $this->prepareDestination(UserRedirectInterface::KEY_LOGOUT, $account);
  }

  /**
   * Set "destination" parameter to do redirect.
   *
   * @param string $key
   *   Configuration key (login or logout).
   * @param \Drupal\Core\Session\AccountInterface|null $account
   *   User account to set destination for.
   */
  protected function prepareDestination($key, AccountInterface $account = NULL) {

    $loggedin_user_roles = array_reverse($this->currentUser->getRoles());
    $role = $loggedin_user_roles[0];
    if ($role) {
      $config = $this->getConfig($key);
      $redirect_url = $config[$role]['redirect_url'];
      if ($redirect_url) {
        if (UrlHelper::isExternal($redirect_url)) {
          $response = new TrustedRedirectResponse($redirect_url);
          $response->send();
        }
        else {
          $url = Url::fromUserInput($redirect_url);
          if ($url instanceof Url) {
            $this->currentRequest->query->set('destination', $url->toString());
          }
        }
      }
      return;
    }
  }

  /**
   * Return requested configuration items (login or logout) ordered by weight.
   *
   * @param string $key
   *   Configuration key (login or logout).
   *
   * @return array
   *   Requested configuration items (login or logout) ordered by weight.
   */
  protected function getConfig($key) {
    $config = $this->config->get($key);
    if ($config) {
      uasort($config, [SortArray::class, 'sortByWeightElement']);
      return $config;
    }

    return [];
  }

}
