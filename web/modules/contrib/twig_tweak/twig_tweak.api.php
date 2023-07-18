<?php

/**
 * @file
 * Hooks specific to the Twig Tweak module.
 */

use Drupal\Component\Utility\Unicode;
use Drupal\node\NodeInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Alters Twig Tweak functions.
 *
 * @param \Twig\TwigFunction[] $functions
 *   Twig functions to alter.
 */
function hook_twig_tweak_functions_alter(array &$functions): void {
  // A simple way to implement lazy loaded global variables.
  $functions[] = new TwigFunction('var', function (string $name) {
    $value = NULL;
    switch ($name) {
      case 'foo':
        $value = 'Foo';
        break;

      case 'bar':
        $value = 'Bar';
        break;
    }
    return $value;
  });
}

/**
 * Alters Twig Tweak filters.
 *
 * @param \Twig\TwigFilter[] $filters
 *   Twig filters to alter.
 */
function hook_twig_tweak_filters_alter(array &$filters): void {
  $filters[] = new TwigFilter('str_pad', 'str_pad');
  $filters[] = new TwigFilter('ucfirst', [Unicode::class, 'ucfirst']);
  $filters[] = new TwigFilter('lcfirst', [Unicode::class, 'lcfirst']);
}

/**
 * Alters Twig Tweak tests.
 *
 * @param \Twig\TwigTest[] $tests
 *   Twig tests to alter.
 */
function hook_twig_tweak_tests_alter(array &$tests): void {
  $tests[] = new TwigTest('outdated', function (NodeInterface $node): bool {
    return \Drupal::time()->getRequestTime() - $node->getCreatedTime() > 3600 * 24 * 365;
  });
}

/**
 * @} End of "addtogroup hooks".
 */
