<?php

namespace Drupal\imce;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\imce\Entity\ImceProfile;

/**
 * Provides a plugin manager for Imce Plugins.
 *
 * @see \Drupal\imce\ImcePluginInterface
 * @see \Drupal\imce\ImcePluginBase
 * @see \Drupal\imce\Annotation\ImcePlugin
 * @see plugin_api
 */
class ImcePluginManager extends DefaultPluginManager {

  /**
   * Available plugin hooks.
   *
   * @var array
   */
  protected $hooks;

  /**
   * Available plugin instances.
   *
   * @var array
   */
  public $instances;

  /**
   * Constructs an ImcePluginManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/ImcePlugin', $namespaces, $module_handler, 'Drupal\imce\ImcePluginInterface', 'Drupal\imce\Annotation\ImcePlugin');
    $this->alterInfo('imce_plugin_info');
    $this->setCacheBackend($cache_backend, 'imce_plugins');
  }

  /**
   * {@inheritdoc}
   */
  protected function findDefinitions() {
    $definitions = parent::findDefinitions();
    // Sort definitions by weight.
    uasort($definitions, [
      'Drupal\Component\Utility\SortArray',
      'sortByWeightElement',
    ]);
    return $definitions;
  }

  /**
   * {@inheritdoc}
   */
  public function getInstance(array $options) {
    if (isset($options['id']) && $id = $options['id']) {
      return isset($this->instances[$id]) ? $this->instances[$id] : $this->createInstance($id);
    }
  }

  /**
   * Returns all available plugin instances.
   *
   * @return array
   *   A an array plugin intances.
   */
  public function getInstances() {
    if (!isset($this->instances)) {
      $this->instances = [];
      foreach ($this->getDefinitions() as $id => $def) {
        $this->instances[$id] = $this->createInstance($id);
      }
    }
    return $this->instances;
  }

  /**
   * Returns available hooks.
   *
   * @return array
   *   An array of method names defined by plugin interface.
   */
  public function getHooks() {
    if (!isset($this->hooks)) {
      $this->hooks = get_class_methods('Drupal\imce\ImcePluginInterface');
    }
    return $this->hooks;
  }

  /**
   * Invokes a hook in all available plugins.
   *
   * @return array
   *   An array of results keyed by plugin id.
   */
  public function invokeAll($hook, &$a = NULL, $b = NULL, $c = NULL) {
    $ret = [];
    if (in_array($hook, $this->getHooks())) {
      foreach ($this->getInstances() as $plugin => $instance) {
        $ret[$plugin] = $instance->$hook($a, $b, $c);
      }
    }
    return $ret;
  }

  /**
   * Returns folder permission definitions.
   */
  public function permissionInfo() {
    $perms = [];
    foreach ($this->invokeAll('permissionInfo') as $data) {
      if ($data) {
        $perms = array_merge($perms, $data);
      }
    }
    return $perms;
  }

  /**
   * Alters an Imce Profile form.
   */
  public function alterProfileForm(array &$form, FormStateInterface $form_state, ImceProfile $imce_profile) {
    return $this->invokeAll('alterProfileForm', $form, $form_state, $imce_profile);
  }

  /**
   * Validates an Imce Profile form.
   */
  public function validateProfileForm(array &$form, FormStateInterface $form_state, ImceProfile $imce_profile) {
    return $this->invokeAll('validateProfileForm', $form, $form_state, $imce_profile);
  }

  /**
   * Processes profile configuration for a user.
   */
  public function processUserConf(array &$conf, AccountProxyInterface $user) {
    return $this->invokeAll('processUserConf', $conf, $user);
  }

  /**
   * Builds imce page.
   */
  public function buildPage(array &$page, ImceFM $fm) {
    return $this->invokeAll('buildPage', $page, $fm);
  }

  /**
   * Alters js response.
   */
  public function alterJsResponse(array &$data, ImceFM $fm) {
    return $this->invokeAll('alterJsResponse', $data, $fm);
  }

  /**
   * Runs an operation handler for the file manager.
   */
  public function handleOperation($op, ImceFM $fm) {
    $plugin = $method = FALSE;
    foreach ($this->getDefinitions() as $p => $def) {
      if (!empty($def['operations'][$op])) {
        $plugin = $p;
        $method = $def['operations'][$op];
      }
    }
    if ($method && $instance = $this->getInstance(['id' => $plugin])) {
      return $instance->$method($fm);
    }
    // Indicate that the operation handler is not found.
    return FALSE;
  }

}
