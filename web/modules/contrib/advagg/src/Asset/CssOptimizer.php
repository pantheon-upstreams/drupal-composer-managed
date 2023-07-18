<?php

namespace Drupal\advagg\Asset;

use Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * The CSS Optimizer.
 */
class CssOptimizer extends AssetOptimizer {

  /**
   * {@inheritdoc}
   */
  public function __construct(ConfigFactoryInterface $config_factory, ContainerAwareEventDispatcher $event_dispatcher, CacheBackendInterface $cache) {
    $this->extension = 'css';
    parent::__construct($config_factory, $event_dispatcher, $cache);
  }

  /**
   * {@inheritdoc}
   */
  protected function addDnsPrefetch(array $asset) {
    $prefetch = [];
    if (!isset($asset['contents'])) {
      return $prefetch;
    }
    $matches = [];
    $pattern = '%url\(\s*+[\'"]?+(http:\/\/|https:\/\/|\/\/)([^\'"()\s]++)[\'"]?+\s*+\)%i';
    preg_match_all($pattern, $asset['contents'], $matches);
    if (!empty($matches[1])) {
      foreach ($matches[1] as $key => $match) {
        $parse = @parse_url($match . $matches[2][$key]);
        if (!empty($parse['host'])) {
          $prefetch[] = $parse['host'];
        }
      }
    }
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
      // stack as long as css.preserve_external is not set.
      elseif (stripos($path, $this->basePath) !== FALSE && !$this->config->get('css.preserve_external')) {
        $asset['type'] = 'file';
        $asset['group'] = CSS_BASE;
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
   * {@inheritdoc}
   */
  protected function optimizeFile(array &$asset, array $data) {
    $contents = $this->updateUrls($data['contents'], $asset['data']);
    if ($this->config->get('css.combine_media') && $asset['media'] !== 'all') {
      $contents = "@media {$asset['media']}{{$contents}}";
      $asset['media'] = 'all';
    }
    $asset_event = new AssetOptimizationEvent($contents, $asset, $data);
    $this->eventDispatcher->dispatch(AssetOptimizationEvent::CSS, $asset_event);
    $contents = $asset_event->getContent();
    $asset = $asset_event->getAsset();

    // If file contents are unaltered return FALSE.
    if ($contents === $data['contents'] && !$this->gZip) {
      return FALSE;
    }
    return $this->writeFile($contents, $data['cid']);
  }

  /**
   * Update any internal urls.
   *
   * @param string $contents
   *   The file contents.
   * @param string $path
   *   The file path.
   *
   * @return string
   *   The updated contents.
   */
  public function updateUrls($contents, $path) {
    // Determine the file's directory including the Drupal base path.
    $directory = base_path() . dirname($path) . '/';

    // Alter all internal url() paths. Leave external paths alone. We don't need
    // to normalize absolute paths here because that will be done later. Also
    // ignore SVG paths (# or %23). Expected form: url("/images/file.jpg") which
    // gets converted to url("${directory}/images/file.jpg").
    return preg_replace('/url\(\s*([\'"]?)(?![a-z]+:|\/+|\#|\%23+)([^\'")]+)([\'"]?)\s*\)/i', 'url(\1' . $directory . '\2\3)', $contents);
  }

}
