<?php

namespace Drupal\layout_builder_browser\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\Core\Routing\RoutingEvents;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    // Override the layout builder choose block controller with ours.
    if ($route = $collection->get('layout_builder.choose_block')) {
      $route->setDefault('_controller', '\Drupal\layout_builder_browser\Controller\BrowserController::browse');
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    // Ensure we alter the controller after other modules, see
    // https://www.drupal.org/node/3129158.
    $events[RoutingEvents::ALTER] = ['onAlterRoutes', -110];
    return $events;
  }

}
