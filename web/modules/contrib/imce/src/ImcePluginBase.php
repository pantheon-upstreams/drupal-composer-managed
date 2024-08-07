<?php

namespace Drupal\imce;

use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\imce\Entity\ImceProfile;

/**
 * Defines a base Imce plugin implementation.
 *
 * @see \Drupal\imce\ImcePluginInterface
 * @see \Drupal\imce\ImcePluginManager
 * @see plugin_api
 */
abstract class ImcePluginBase extends PluginBase implements ImcePluginInterface {

  /**
   * {@inheritdoc}
   */
  public function permissionInfo() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function alterProfileForm(array &$form, FormStateInterface $form_state, ImceProfile $imce_profile) {
  }

  /**
   * {@inheritdoc}
   */
  public function validateProfileForm(array &$form, FormStateInterface $form_state, ImceProfile $imce_profile) {
  }

  /**
   * {@inheritdoc}
   */
  public function processUserConf(array &$conf, AccountProxyInterface $user) {
  }

  /**
   * {@inheritdoc}
   */
  public function buildPage(array &$page, ImceFM $fm) {
  }

  /**
   * {@inheritdoc}
   */
  public function alterJsResponse(array &$data, ImceFM $fm) {
  }

}
