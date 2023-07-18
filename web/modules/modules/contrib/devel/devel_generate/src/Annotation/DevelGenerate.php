<?php

namespace Drupal\devel_generate\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a DevelGenerate annotation object.
 *
 * DevelGenerate handle the bulk creation of entites.
 *
 * Additional annotation keys for DevelGenerate can be defined in
 * hook_devel_generate_info_alter().
 *
 * @Annotation
 *
 * @see \Drupal\devel_generate\DevelGeneratePluginManager
 * @see \Drupal\devel_generate\DevelGenerateBaseInterface
 */
class DevelGenerate extends Plugin {
  /**
   * The human-readable name of the DevelGenerate type.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * A short description of the DevelGenerate type.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $description;

  /**
   * A url to access the plugin settings form.
   *
   * @var string
   */
  public $url;

  /**
   * The permission required to access the plugin settings form.
   *
   * @var string
   */
  public $permission;

  /**
   * The name of the DevelGenerate class.
   *
   * This is not provided manually, it will be added by the discovery mechanism.
   *
   * @var string
   */
  public $class;

  /**
   * An array of settings passed to the DevelGenerate settingsForm.
   *
   * The keys are the names of the settings and the values are the default
   * values for those settings.
   *
   * @var array
   */
  public $settings = [];

  /**
   * Modules that should be enabled in order to make the plugin discoverable.
   *
   * @var array
   */
  public $dependencies = [];

}
