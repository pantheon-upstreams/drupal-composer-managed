<?php

use Drupal\Core\Render\Element;

/**
 * Prepares variables for user request templates.
 *
 * Default template: user-request.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user request information and any
 *     fields attached to the entity.
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_user_request(array &$variables) {
  $variables['view_mode'] = $variables['elements']['#view_mode'];
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}

