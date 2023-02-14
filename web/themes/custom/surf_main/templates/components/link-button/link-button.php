<?php

/**
 * @file
 * Component PHP integration.
 */

use Drupal\cl_components\Plugin\Component;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements cl_component_COMPONENT_form_alter().
 */
function cl_component_link_button_form_alter($form, FormStateInterface $form_state, Component $component): array {
  $form['data']['icon_type']['#description'] = \t('The component type will determine the icon in the button.');
  $form['data']['icon_type']['#default_value'] = $form['data']['icon_type']['#default_value'] ?? 'arrow';
  $form['data']['text']['#placeholder'] = \t('Click me!');
  return $form;
}
