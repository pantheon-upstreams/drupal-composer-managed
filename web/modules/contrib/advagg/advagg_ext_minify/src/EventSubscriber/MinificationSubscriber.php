<?php

namespace Drupal\advagg_ext_minify\EventSubscriber;

use Drupal\advagg\Asset\AssetOptimizationEvent;
use Drupal\advagg\Asset\SingleAssetOptimizerBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Subscribe to asset optimization events and minify assets.
 */
class MinificationSubscriber implements EventSubscriberInterface {

  /**
   * The minifier.
   *
   * @var \Drupal\advagg\Asset\SingleAssetOptimizerBase
   */
  protected $minifier;

  /**
   * The minifier configuration.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * Construct the optimizer instance.
   *
   * @param \Drupal\advagg\Asset\SingleAssetOptimizerBase $minifier
   *   The minifier.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(SingleAssetOptimizerBase $minifier, ConfigFactoryInterface $config_factory) {
    $this->minifier = $minifier;
    $this->config = $config_factory->get('advagg_ext_minify.settings');
  }

  /**
   * Pass the minification over to the minification service.
   *
   * @param \Drupal\advagg\Asset\AssetOptimizationEvent $asset
   *   The asset optimization event.
   */
  public function minifyCss(AssetOptimizationEvent $asset) {
    if (!$this->isEnabled('css')) {
      return;
    }
    $content = $asset->getContent();
    $content = $this->minifier->css($content, $asset->getAsset(), $asset->getData());
    $asset->setContent($content);
  }

  /**
   * Pass the minification over to the minification service.
   *
   * @param \Drupal\advagg\Asset\AssetOptimizationEvent $asset
   *   The asset optimization event.
   */
  public function minifyJs(AssetOptimizationEvent $asset) {
    if (!$this->isEnabled('js')) {
      return;
    }
    $content = $asset->getContent();
    $content = $this->minifier->js($content, $asset->getAsset(), $asset->getData());
    $asset->setContent($content);
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      AssetOptimizationEvent::CSS => ['minifyCss', 0],
      AssetOptimizationEvent::JS => ['minifyJs', 0],
    ];
  }

  /**
   * Check if the external minifier is enabled & configured for this asset type.
   *
   * @param string $type
   *   The asset type.
   *
   * @return bool
   *   Whether to run the external minifier or not.
   */
  protected function isEnabled($type) {
    if (!$this->config->get("{$type}_enabled")) {
      return FALSE;
    }
    if (!$this->config->get("{$type}_cmd")) {
      return FALSE;
    }
    return TRUE;
  }

}
