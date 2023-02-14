<?php

namespace Drupal\surf_dashboard\Menu;

use Drupal\Core\Menu\MenuLinkDefault;
use Drupal\Core\Menu\StaticMenuLinkOverridesInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Routing\RouteProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DashboardMenuLink extends MenuLinkDefault {

  protected $routeMatch;

  protected $routeProvider;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, StaticMenuLinkOverridesInterface $static_override, CurrentRouteMatch $current_route_match, RouteProviderInterface $route_provider) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $static_override);
    $this->routeMatch = $current_route_match;
    $this->routeProvider = $route_provider;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('menu_link.static.overrides'),
      $container->get('current_route_match'),
      $container->get('router.route_provider')
    );
  }

  public function isEnabled() {
    $route_name = $this->getRouteName();
    if (empty($route_name)) {
      return 0;
    }
    if (!$this->getUserRouteParameter()) {
      return 0;
    }
    return (count($this->routeProvider->getRoutesByNames([$route_name])) === 1);
  }

  private function getUserRouteParameter() {
    return $this->routeMatch->getParameter('user');
  }


  public function getRouteParameters() {
    $parameters = parent::getRouteParameters();
    if ($user = $this->getUserRouteParameter()) {
      $parameters['user'] = $user->id();
    }
    return $parameters;
  }

  public function getRouteName() {
    $options = $this->getOptions();
    $page_id = $options['page_manager_page_id'];
    $variant_id = $options['variant_id'];
    return "page_manager.page_view_{$page_id}_{$page_id}-$variant_id";
  }

  public function getCacheContexts() {
    return ['url.path'];
  }


}
