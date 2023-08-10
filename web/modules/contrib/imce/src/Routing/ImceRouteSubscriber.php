<?php

namespace Drupal\imce\Routing;

use Symfony\Component\Routing\RouteCollection;
use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Listens to the dynamic route events.
 */
class ImceRouteSubscriber extends RouteSubscriberBase {

  /**
   * The constructor method.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The constructor method.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Defines the configuration object factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    // Conditionally declare imce.page an admin route.
    if ($route = $collection->get('imce.page')) {
      $config = $this->configFactory->get('imce.settings');
      if ($config->get('admin_theme')) {
        $route->setOption('_admin_route', TRUE);
      }
    }
  }

}
