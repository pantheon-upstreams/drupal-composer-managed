<?php

namespace Drupal\advagg_bundler\EventSubscriber;

use Drupal\advagg\Asset\AssetOptimizationEvent;
use Drupal\advagg\Asset\SingleAssetOptimizerBase;
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
   * Construct the optimizer instance.
   *
   * @param \Drupal\advagg\Asset\SingleAssetOptimizerBase $minifier
   *   The minifier.
   */
  public function __construct(SingleAssetOptimizerBase $minifier) {
    $this->minifier = $minifier;
  }

  /**
   * Pass the minification over to the minification service.
   *
   * @param \Drupal\advagg\Asset\AssetOptimizationEvent $asset
   *   The optimization event.
   */
  public function minify(AssetOptimizationEvent $asset) {
    $content = $asset->getContent();
    $content = $this->minifier->optimize($content, $asset->getAsset(), $asset->getData());
    $this->minifier->addLicense($content, $asset->getAsset()['data']);
    $asset->setContent($content);
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [AssetOptimizationEvent::JS => ['minify', 0]];
  }

}
