<?php

namespace Drupal\advagg_old_ie_compatibility\EventSubscriber;

use Drupal\advagg\Asset\AssetOptimizationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Subscribe to CSs asset optimization events and count selectors.
 */
class CssSubscriber implements EventSubscriberInterface {

  /**
   * Scan the asset, count the selectors and save selector count to asset array.
   *
   * The counting method is very rough and may have inaccuracies - especially if
   * there are media queries in the file. Since we only care about the maximum,
   * this is acceptable since that reduces performance hit and an exact number
   * is unimportant.
   *
   * @param \Drupal\advagg\Asset\AssetOptimizationEvent $asset
   *   The asset optimization event.
   */
  public function scan(AssetOptimizationEvent $asset) {
    $asset_array = $asset->getAsset();
    $asset_array['selectors'] = substr_count($asset->getContent(), '{');
    $asset->setAsset($asset_array);
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [AssetOptimizationEvent::CSS => ['scan', 100]];
  }

}
