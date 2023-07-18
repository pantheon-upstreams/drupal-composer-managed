<?php

namespace Drupal\imce;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\imce\Entity\ImceProfile;

/**
 * Defines an interface for Imce plugins.
 *
 * @see \Drupal\imce\ImcePluginBase
 * @see \Drupal\imce\ImcePluginManager
 * @see plugin_api
 */
interface ImcePluginInterface extends PluginInspectionInterface {

  /**
   * Returns folder permission definitions.
   *
   * @return array
   *   An array of id:label pairs.
   */
  public function permissionInfo();

  /**
   * Alters entity form of an Imce Profile.
   */
  public function alterProfileForm(array &$form, FormStateInterface $form_state, ImceProfile $imce_profile);

  /**
   * Validates entity form of an Imce Profile.
   */
  public function validateProfileForm(array &$form, FormStateInterface $form_state, ImceProfile $imce_profile);

  /**
   * Processes profile configuration for a user.
   */
  public function processUserConf(array &$conf, AccountProxyInterface $user);

  /**
   * Builds imce page by adding required libraries and elements.
   */
  public function buildPage(array &$page, ImceFM $fm);

  /**
   * Alters js response.
   */
  public function alterJsResponse(array &$data, ImceFM $fm);

}
