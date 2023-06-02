<?php

namespace Drupal\admin_js_attach\Plugin\AlterDecorator;

use Drupal\Core\Asset\AssetCollectionInterface;
use Drupal\Core\Asset\AssetResolverInterface;
use Drupal\Core\Asset\AssetSchedulingStateInterface;
use Drupal\Core\Asset\AssetCssOptimizerInterface;
use Drupal\Core\Asset\AssetJsOptimizerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Attaches the custom JavaScript file only when using the admin theme.
 *
 * @AlterDecorator(
 *   id = "admin_js_attach",
 *   label = @Translation("Admin JS Attach"),
 *   drupal_version = "8",
 *   drupal_version_end = "8",
 * )
 */
class AdminThemeAssetAlterDecorator extends \Drupal\Core\Plugin\AlterDecorator\AssetAlterDecoratorBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs an AdminThemeAssetAlterDecorator object.
   *
   * @param \Drupal\Core\Asset\AssetCollectionInterface $collection
   *   The asset collection.
   * @param \Drupal\Core\Asset\AssetResolverInterface $asset_resolver
   *   The asset resolver.
   * @param \Drupal\Core\Asset\AssetSchedulingStateInterface $asset_scheduling_state
   *   The asset scheduling state.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\Core\Extension\ThemeHandlerInterface $theme_handler
   *   The theme handler.
   * @param \Drupal\Core\Asset\AssetCssOptimizerInterface $css_optimizer
   *   The CSS optimizer.
   * @param \Drupal\Core\Asset\AssetJsOptimizerInterface $js_optimizer
   *   The JavaScript optimizer.
   */
  public function __construct(
    AssetCollectionInterface $collection,
    AssetResolverInterface $asset_resolver,
    AssetSchedulingStateInterface $asset_scheduling_state,
    ModuleHandlerInterface $module_handler,
    ThemeHandlerInterface $theme_handler,
    AssetCssOptimizerInterface $css_optimizer,
    AssetJsOptimizerInterface $js_optimizer
  ) {
    parent::__construct($collection, $asset_resolver, $asset_scheduling_state, $module_handler, $theme_handler, $css_optimizer, $js_optimizer);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('asset.css.collection'),
      $container->get('asset.resolver'),
      $container->get('asset.scheduling_state'),
      $container->get('module_handler'),
      $container->get('theme_handler'),
      $container->get('asset.css.optimizer'),
      $container->get('asset.js.optimizer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function alter(array &$assets) {
    // Only attach the custom JavaScript file when using the admin theme.
    dump($this->themeHandler->getDefault());
    if ($this->themeHandler->getDefault() === 'gin') {
      $assets[] = [
        'type' => 'file',
        'data' => 'js/custom_field_visibility.js',
        'scope' => 'footer',
        'weight' => 100,
      ];
    }
  }

}
