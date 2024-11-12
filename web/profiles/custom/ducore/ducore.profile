<?php

/**
 * @file
 * Enables modules and site configuration for the DU Core profile.
 */

use Drupal\Core\Form\FormStateInterface;
use Drupal\user\UserInterface;

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

/**
* Impliment to give user default role if they are on
* the system or campus support lists
*/
function ducore_user_presave(UserInterface $user) {
  // IMPRORTANT! Because of change to using CammelCase in usernames at DU wasn't 
  // retoactively applied, we are evaluating the match in lowercase.
  $support_eas = array('kevin.reynen', 'kent.houge', 'charles.r.jones','joshua.mcgehee','alex.martinez', 'chris.hewitt');
  $support_ur =  array('mac.whitney', 'nathan.boorom', 'staci.striegnitz', 'sherry.liang', 'anastasia.vylegzhanina', 'james.e.thomas');
  // @TODO - These arrays should be YML files or API endpoint that can be 
  // easily editted outside the PHP
  // Check to see if this user is on the list of campus or system support users
  if (in_array(strtolower($user->getAccountName()), $support_eas)) {
    $user->addRole('administrator');
  }
  if (in_array(strtolower($user->getAccountName()), $support_ur)) {
    $user->addRole('site_admin');
  }
}

