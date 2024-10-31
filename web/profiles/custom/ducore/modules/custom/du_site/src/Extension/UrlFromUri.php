<?php

namespace Drupal\du_site\Extension;

use Drupal\Core\Url;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class UrlFromUri.
 *
 * @package Drupal\du_site
 */
class UrlFromUri extends AbstractExtension {

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'url_from_uri';
  }

  /**
   * In this function we can declare the extension function.
   */
  public function getFunctions() {
    return [
      new TwigFunction('url_from_uri', [$this, 'getUrlFromUri'], ['is_safe' => ['html']]),
    ];
  }

  /**
   * The php function to convert url to url.
   */
  public function getUrlFromUri($uri) {
    // Parse uri to find out what type it is.
    $parts = preg_split('/:/', $uri);

    if ($parts[0] == 'entity') {
      $url = Url::fromUri($uri);
    }
    elseif ($parts[0] == 'internal') {
      $url = Url::fromUri($uri);
    }
    else {
      $url = $uri;
    }
    return $url;
  }

}
