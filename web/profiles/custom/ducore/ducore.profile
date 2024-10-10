<?php

/**
 * @file
 * Enables modules and site configuration for the DU Core profile.
 */

use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_FORM_ID_alter() for install_configure_form().
 *
 * Allows the profile to alter the site configuration form.
 */
function ducore_form_install_configure_form_alter(&$form, FormStateInterface $form_state) {
  // We'll add custom alterations to the site configuration form here.
}

/**
 * Implements hook_install_tasks().
 */
function ducore_install_tasks(&$install_state) {
  // We'll define custom installation tasks here.
  $tasks = [
    // Blank for now but any tasks can be added here later on.
  ];
  return $tasks;
}

/**
 * Implements hook_install_tasks_alter().
 */
function ducore_install_tasks_alter(&$tasks, $install_state) {
  // Alter any installation tasks here.
}