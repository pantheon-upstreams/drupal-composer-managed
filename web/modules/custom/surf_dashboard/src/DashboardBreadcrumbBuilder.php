<?php

namespace Drupal\surf_dashboard;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatch;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\system\PathBasedBreadcrumbBuilder;
use http\Url;

class DashboardBreadcrumbBuilder extends PathBasedBreadcrumbBuilder implements BreadcrumbBuilderInterface {

  public function applies(RouteMatchInterface $route_match) {
    /** @var \Drupal\page_manager\Entity\Page $page */
    if (!$page = $route_match->getparameter('page_manager_page')) {
      return FALSE;
    }
    $is_dashboard_page = strpos($page->id(), 'dashboard_') === 0;

    return $is_dashboard_page;
  }

  public function build(RouteMatchInterface $route_match) {
    $breadcrumb = new Breadcrumb();
    $links = [];

    // Add the url.path.parent cache context. This code ignores the last path
    // part so the result only depends on the path parents.
    $breadcrumb->addCacheContexts(['url.path.parent', 'url.path.is_front']);

    $exclude = [];
    $uid_from_path = \Drupal::routeMatch()->getParameter('user')->id();
    $exclude['/user'] = TRUE;
    $exclude['/user/' . $uid_from_path] = TRUE;
    //$exclude['/user/' . $uid_from_path . '/dashboard'] = TRUE;
    $exclude[$this->currentPath->getPath()] = TRUE;
    $path = trim($this->context->getPathInfo(), '/');
    $path_elements = explode('/', $path);
    while (count($path_elements) > 1) {
      array_pop($path_elements);
      // Copy the path elements for up-casting.
      $route_request = $this->getRequestForPath('/' . implode('/', $path_elements), $exclude);
      if ($route_request) {
        $route_match = RouteMatch::createFromRequest($route_request);
        $access = $this->accessManager->check($route_match, $this->currentUser, NULL, TRUE);
        // The set of breadcrumb links depends on the access result, so merge
        // the access result's cacheability metadata.
        $breadcrumb = $breadcrumb->addCacheableDependency($access);
        if ($access->isAllowed()) {
          $title = $this->titleResolver->getTitle($route_request, $route_match->getRouteObject());
          if (!isset($title)) {
            // Fallback to using the raw path component as the title if the
            // route is missing a _title or _title_callback attribute.
            $title = str_replace(['-', '_'], ' ', Unicode::ucfirst(end($path_elements)));
          }
          $url = \Drupal\Core\Url::fromRouteMatch($route_match);
          $links[] = new Link($title, $url);
        }
      }
    }

    return $breadcrumb->setLinks(array_reverse($links));
  }

}
