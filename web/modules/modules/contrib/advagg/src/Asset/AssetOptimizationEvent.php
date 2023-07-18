<?php

namespace Drupal\advagg\Asset;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * @defgroup advagg_optimization_event Asset Optimization Event
 *
 * @{
 * The Asset Optimization Event provides 2 separate events, one for JavaScript
 * and one for CSS optimization. Both provide equivalent data and is only
 * separated to allow easy differentiation/choice of events to subscribe to.
 *
 * The AssetOptimizationEvent::JS is fired by \Drupal\advagg\Asset\JsOptimizer,
 * The AssetOptimizationEvent:CSS is fired by \Drupal\advagg\Asset\CssOptimizer.
 *
 * There are multiple examples within the Advanced Aggregates submodules of
 * subscribing to one or both of the events.
 */

/**
 * AssetOptimizationEvent data holder.
 */
class AssetOptimizationEvent extends Event {

  /**
   * The Event ID for css optimization.
   */
  const CSS = 'advagg.optimize_css';

  const JS = 'advagg.optimize_js';

  /**
   * The asset content.
   *
   * @var string
   */
  protected $content;

  /**
   * A core single asset definition array.
   *
   * @var array
   */
  protected $asset;

  /**
   * A single asset advagg cache array.
   *
   * @var array
   */
  protected $data;

  /**
   * AssetOptimizationEvent constructor.
   *
   * @param string $content
   *   The asset content.
   * @param array $asset
   *   The asset core definition array.
   * @param array $data
   *   The cache data about the asset.
   */
  public function __construct($content, array $asset, array $data) {
    $this->content = $content;
    $this->asset = $asset;
    $this->data = $data;
  }

  /**
   * Get the current content.
   *
   * @return string
   *   The current content.
   */
  public function getContent() {
    return $this->content;
  }

  /**
   * Set new content.
   *
   * @param string $content
   *   The new content.
   */
  public function setContent($content) {
    $this->content = $content;
  }

  /**
   * Get the asset array.
   *
   * @return array
   *   The current core asset definition array.
   */
  public function getAsset() {
    return $this->asset;
  }

  /**
   * Update the asset array.
   *
   * @param array $asset_array
   *   The updated asset definition array.
   */
  public function setAsset(array $asset_array) {
    $this->asset = $asset_array;
  }

  /**
   * Get the cache data.
   *
   * @return array
   *   The current cache data array.
   */
  public function getData() {
    return $this->data;
  }

  /**
   * Update the cache data.
   *
   * @param array $data
   *   The new cache data array.
   */
  public function setData(array $data) {
    $this->data = $data;
  }

}

/**
 * @} End of "defgroup advagg_optimization_event".
 */
