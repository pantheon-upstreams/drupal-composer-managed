<?php

namespace Drupal\advagg\Asset;

use Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * The JavaScript Optimizer.
 */
class JsOptimizer extends AssetOptimizer {

  /**
   * {@inheritdoc}
   */
  public function __construct(ConfigFactoryInterface $config_factory, ContainerAwareEventDispatcher $event_dispatcher, CacheBackendInterface $cache) {
    $this->extension = 'js';
    parent::__construct($config_factory, $event_dispatcher, $cache);
  }

  /**
   * {@inheritdoc}
   */
  protected function addDnsPrefetch(array $asset) {
    // Check if Google Ad Manager and add DNS prefetch.
    $prefetch = $this->testForGoogleAdManager($asset['data']);

    // Check if Google Analytics and add DNS prefetch.
    $prefetch += $this->testForGoogleAnalytics($asset['data']);

    return $prefetch;
  }

  /**
   * {@inheritdoc}
   */
  protected function fixType(array &$asset) {
    // Default asset type to file if not set/invalid.
    if (!in_array($asset['type'], ['file', 'external', 'settings'])) {
      $asset['type'] = 'file';
    }

    $path = $asset['data'];

    if ($asset['type'] === 'external') {
      // If type is external but path doesn't start with http, https, or //
      // change it to file.
      if (stripos($path, 'http') !== 0 && stripos($path, '//') !== 0) {
        $asset['type'] = 'file';
      }
      // If type is external and starts with http, https, or // but points to
      // this host change to file, but move it to the top of the aggregation
      // stack as long as js.preserve_external is not set.
      elseif (stripos($path, $this->basePath) !== FALSE && !$this->config->get('js.preserve_external')) {
        $asset['type'] = 'file';
        $asset['group'] = JS_LIBRARY;
        $asset['every_page'] = TRUE;
        $asset['weight'] = -40000;
        $asset['data'] = substr($asset['data'], stripos($asset['data'], $this->basePath) + $this->basePathLen);
      }
    }

    // If type is file but it starts with http, https, or // change it to
    // external.
    elseif ($asset['type'] === 'file' && (stripos($path, 'http') === 0 || (stripos($path, '//') === 0))) {
      $asset['type'] = 'external';
    }

  }

  /**
   * Test if the provided path is from Google Ad Manager and add DNS entries.
   *
   * @param string $path
   *   The path to check.
   *
   * @return array
   *   Array of prefetch domains if file is from Google Ad Manager.
   */
  private function testForGoogleAdManager($path) {
    $prefetch = [];
    if (strpos($path, '/google_service.') == FALSE) {
      return $prefetch;
    }

    // Domains in the google_service.js file.
    $prefetch[] = 'https://csi.gstatic.com';
    $prefetch[] = 'https://pubads.g.doubleclick.net';
    $prefetch[] = 'https://partner.googleadservices.com';
    $prefetch[] = 'https://securepubads.g.doubleclick.net';

    // Domains in the google_ads.js file.
    $prefetch[] = 'https://pagead2.googlesyndication.com';

    // Other domains that usually get hit.
    $prefetch[] = 'https://cm.g.doubleclick.net';
    $prefetch[] = 'https://tpc.googlesyndication.com';
    return $prefetch;
  }

  /**
   * Test if the provided path is from Google Analytics and add DNS entries.
   *
   * @param string $path
   *   The path to check.
   *
   * @return array
   *   Empty array or an array to prefetch if file is from Google Analytics.
   */
  private function testForGoogleAnalytics($path) {
    $prefetch = [];
    if (strpos($path, 'GoogleAnalytics') == FALSE && strpos($path, 'google-analytics') == FALSE) {
      return $prefetch;
    }
    $prefetch[] = 'https://ssl.google-analytics.com';
    $prefetch[] = 'https://stats.g.doubleclick.net';
    return $prefetch;
  }

  /**
   * {@inheritdoc}
   */
  protected function optimizeFile(array &$asset, array $data) {
    $asset_event = new AssetOptimizationEvent($data['contents'], $asset, $data);
    $this->eventDispatcher->dispatch(AssetOptimizationEvent::JS, $asset_event);
    $contents = $asset_event->getContent();
    $asset = $asset_event->getAsset();

    // If file contents are unaltered return FALSE.
    if ($contents === $data['contents'] && !$this->gZip) {
      return FALSE;
    }
    return $this->writeFile($contents, $data['cid']);
  }

}
