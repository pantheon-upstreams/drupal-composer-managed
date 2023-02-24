<?php

namespace Drupal\surf_core\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Twig extension with some useful functions and filters.
 *
 * The extension consumes quite a lot of dependencies. Most of them are not used
 * on each page request. For performance reasons services are wrapped in static
 * callbacks.
 */
class TwigExtension extends AbstractExtension {

  public function getFilters() {
    return [
      new TwigFilter('addModifiers', [$this, 'addModifiers']),
    ];
  }

  public function addModifiers($baseClass, $modifiers = [], $separator = '--') {
    $result = [$baseClass];

    if (!empty($modifiers) && !is_array($modifiers)) {
      $modifiers = [$modifiers];
    }

    foreach ($modifiers as $modifier) {
      $result[] = $baseClass . $separator . $modifier;
    }

    return $result;
  }

}
