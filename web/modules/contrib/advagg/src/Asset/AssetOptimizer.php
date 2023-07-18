<?php

namespace Drupal\advagg\Asset;

use Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Component\Utility\Crypt;
use Drupal\Core\File\FileSystemInterface;

/**
 * Defines the base AdvAgg optimizer.
 */
abstract class AssetOptimizer {

  /**
   * The base path.
   *
   * @var false|string
   */
  protected $basePath;

  /**
   * The base path length.
   *
   * @var int
   */
  protected $basePathLen;

  /**
   * Checks for and if found fixes incorrectly set asset types.
   *
   * @param array $asset
   *   A core single asset definition array.
   */
  abstract protected function fixType(array &$asset);

  /**
   * Asset type (css or js).
   *
   * @var string
   */
  protected $extension;

  /**
   * A config object for the advagg configuration.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * Event Dispatcher service.
   *
   * @var \Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher
   */
  protected $eventDispatcher;

  /**
   * The AdvAgg cache.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cache;

  /**
   * Config level of caching of assets.
   *
   * @var int
   */
  protected $cacheLevel;

  /**
   * The cache time.
   *
   * @var int
   */
  protected $cacheTime;

  /**
   * The file system.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * Config to control fixing the asset type (file, external).
   *
   * @var bool
   */
  protected $fixType;

  /**
   * Whether or not to gzip assets.
   *
   * @var bool
   */
  protected $gZip;

  /**
   * Whether or not to brotli compress assets.
   *
   * @var bool
   */
  protected $brotli;

  /**
   * Array of domains to prefetch. Copied to $GLOBALS for later use.
   *
   * @var array
   */
  protected $dnsPrefetch;

  /**
   * Constructs the Optimizer object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   A config factory for retrieving required config objects.
   * @param \Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher $event_dispatcher
   *   The event dispatcher.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   The AdvAgg cache.
   */
  public function __construct(ConfigFactoryInterface $config_factory, ContainerAwareEventDispatcher $event_dispatcher, CacheBackendInterface $cache) {
    $this->config = $config_factory->get('advagg.settings');
    $this->eventDispatcher = $event_dispatcher;
    $this->cache = $cache;

    $this->dnsPrefetch = [];
    $this->cacheLevel = $this->config->get('cache_level');

    $this->fileSystem = \Drupal::service('file_system');

    $this->fixType = $this->config->get("{$this->extension}.fix_type");
    if ($this->fixType) {
      $this->basePath = substr($GLOBALS['base_root'] . $GLOBALS['base_path'], strpos($GLOBALS['base_root'] . $GLOBALS['base_path'], '//') + 2);
      $this->basePathLen = strlen($this->basePath);
    }

    $this->gZip = $this->shouldGZip();
    $this->brotli = $this->shouldBrotli();
  }

  /**
   * Process a core asset array.
   *
   * @param array $assets
   *   The core asset array (css or js) to process.
   */
  public function processAssetArray(array &$assets) {
    $protocol_relative = $this->config->get('path.convert.absolute_to_protocol_relative');
    $force_https = $this->config->get('path.convert.force_https');

    foreach ($assets as &$asset) {
      if (!is_string($asset['data'])) {
        continue;
      }

      // Fix type if it was incorrectly set.
      if ($this->fixType) {
        $this->fixType($asset);
      }

      if ($asset['type'] === 'file' && $asset['preprocess']) {
        if (!is_readable($asset['data'])) {
          continue;
        }
        $this->scanFile($asset);
      }

      elseif ($asset['type'] === 'external') {
        if ($force_https) {
          $asset['data'] = $this->convertPathForceHttps($asset['data']);
        }
        elseif ($protocol_relative) {
          $asset['data'] = $this->convertPathProtocolRelative($asset['data']);
        }

        $scheme = parse_url($asset['data'], PHP_URL_SCHEME);
        $host = parse_url($asset['data'], PHP_URL_HOST);
        $asset_url = isset($scheme) ? "{$scheme}://{$host}" : "//{$host}";

        $this->dnsPrefetch[] = $asset_url;
      }
    }
    if (!isset($GLOBALS['_advagg_prefetch'])) {
      $GLOBALS['_advagg_prefetch'] = [];
    }
    $GLOBALS['_advagg_prefetch'] += $this->dnsPrefetch;
  }

  /**
   * Given a filename calculate various hashes, gather meta data then optimize.
   *
   * If any file optimizations are applied, updates the asset array.
   * Also if enabled preemptively creates compressed versions.
   *
   * @param array $asset
   *   A core asset array.
   */
  protected function scanFile(array &$asset) {
    // Clear PHP's internal file status cache.
    clearstatcache(TRUE, $asset['data']);
    $cid = Crypt::hashBase64($asset['data'] . $this->config->get('global_counter'));
    $cached = $this->cache->get($cid);
    if ($cached && file_exists($cached->data['file'])) {
      if ($this->config->get('css.combine_media') && isset($asset['media']) && $asset['media'] !== 'all') {
        $asset['media'] = 'all';
      }
      $asset['size'] = $cached->data['filesize'];

      if ($this->cacheLevel === 3) {
        $asset['data'] = $cached->data['file'];
        $this->dnsPrefetch += $cached->data['prefetch'];
        return;
      }

      $data = [
        'filesize' => (int) @filesize($asset['data']),
        'mtime' => @filemtime($asset['data']),
      ];

      if ($this->cacheLevel === 2) {
        if ($cached->data['mtime'] === $data['mtime']) {
          $asset['data'] = $cached->data['file'];
          $this->dnsPrefetch += $cached->data['prefetch'];
          return;
        }
      }

      $data['contents'] = @file_get_contents($asset['data']);
      $data['hash'] = Crypt::hashBase64($data['contents']);

      if ($this->cacheLevel === 1) {
        if ($cached->data['hash'] === $data['hash']) {
          $asset['data'] = $cached->data['file'];
          $this->dnsPrefetch += $cached->data['prefetch'];
          return;
        }
      }
    }

    if (empty($data)) {
      $data = [
        'filesize' => (int) @filesize($asset['data']),
        'mtime' => @filemtime($asset['data']),
        'contents' => @file_get_contents($asset['data']),
      ];
      $data['hash'] = Crypt::hashBase64($data['contents']);
    }
    $data['cid'] = $cid;
    $asset['size'] = $data['filesize'];

    if ($data['file'] = $this->optimizeFile($asset, $data)) {
      $asset['contents'] = $data['contents'];
      $data['prefetch'] = $this->addDnsPrefetch($asset);
      $this->dnsPrefetch += $data['prefetch'];
      $data['original'] = $asset['data'];
      unset($data['contents']);
      unset($data['cid']);
      $this->cache->set($cid, $data, $this->getCacheTime(), ['advagg']);
      $asset['data'] = $data['file'];
    }
  }

  /**
   * The filename for the CSS or JS optimized file is the cid.
   *
   * The CID is generated from the hashed original filename.
   *
   * @param string $data
   *   The content to output.
   * @param string $cid
   *   The unique segment of the filename.
   *
   * @return bool|string
   *   FALSE or the saved filename.
   */
  protected function writeFile($data, $cid) {
    // Prefix filename to prevent blocking by firewalls which reject files
    // starting with "ad*".
    // Create the css/ or js/ path within the files folder.
    $path = 'public://' . $this->extension . '/optimized';
    $version = Crypt::hashBase64($data);
    $uri = "{$path}/{$this->extension}_{$cid}.{$version}.{$this->extension}";

    // Create the CSS or JS file.
    $this->fileSystem->prepareDirectory($path, FileSystemInterface::CREATE_DIRECTORY);
    if (!file_exists($uri)) {
      if (!$this->fileSystem->saveData($data, $uri, FileSystemInterface::EXISTS_REPLACE)) {
        return FALSE;
      }
    }

    // If CSS/JS gzip compression is enabled and the zlib extension is available
    // then create a gzipped version of this file. This file is served
    // conditionally to browsers that accept gzip using .htaccess rules.
    if ($this->gZip && !file_exists($uri . '.gz')) {
      $this->fileSystem->saveData(gzencode($data, 9, FORCE_GZIP), $uri . '.gz', FileSystemInterface::EXISTS_REPLACE);
    }

    // If brotli compression is enabled and available, create br compressed
    // files and serve conditionally via .htaccess rules.
    if ($this->brotli && !file_exists($uri . '.br')) {
      $this->fileSystem->saveData(brotli_compress($data, 11, BROTLI_TEXT), $uri . '.br', FileSystemInterface::EXISTS_REPLACE);
    }
    return $uri;
  }

  /**
   * Determine if settings and available PHP modules allow GZipping assets.
   *
   * @return bool
   *   True if asset type can/should be gzipped.
   */
  protected function shouldGZip() {
    if (extension_loaded('zlib') && \Drupal::config('system.performance')->get($this->extension . '.gzip')) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Determine if settings and available PHP modules allow brotli-ing assets.
   *
   * @return bool
   *   True if asset type can/should be brotli-ed.
   */
  protected function shouldBrotli() {
    if (function_exists('brotli_compress') && $this->config->get($this->extension . '.brotli')) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Get how long to cache an asset. Varies on cache level setting.
   *
   * @return int
   *   The seconds to cache the data for.
   */
  protected function getCacheTime() {
    if ($this->cacheTime) {
      return $this->cacheTime;
    }
    $this->cacheTime = (int) microtime(TRUE);

    // 1 Day.
    if ($this->cacheLevel === 1) {
      $this->cacheTime += 86400;
    }

    // 1 Week.
    elseif ($this->cacheLevel === 2) {
      $this->cacheTime += 604800;
    }

    // 1 Month.
    elseif ($this->cacheLevel === 3) {
      $this->cacheTime += 2419200;
    }
    return $this->cacheTime;
  }

  /**
   * Perform any in-place optimization & pass to event for further optimization.
   *
   * @param array $asset
   *   Core single asset definition array.
   * @param array $data
   *   An array of extra file information (hashes, modification time etc).
   *
   * @return bool|string
   *   False if contents unchanged or the new file path if optimized.
   */
  abstract protected function optimizeFile(array &$asset, array $data);

  /**
   * Extract any domains to prefetch DNS.
   *
   * @param array $asset
   *   A core asset definition array.
   *
   * @return array
   *   An array of domains to prefetch.
   */
  abstract protected function addDnsPrefetch(array $asset);

  /**
   * Converts absolute paths to be protocol relative paths.
   *
   * @param string $path
   *   Path to check.
   *
   * @return string
   *   The converted path or the original path if already protocol relative.
   */
  protected function convertPathProtocolRelative($path) {
    if (strpos($path, 'https://') === 0) {
      $path = substr($path, 6);
    }
    elseif (strpos($path, 'http://') === 0) {
      $path = substr($path, 5);
    }
    return $path;
  }

  /**
   * Convert http:// to https://.
   *
   * @param string $path
   *   Path to check.
   *
   * @return string
   *   The modified path or the original if already https or relative.
   */
  protected function convertPathForceHttps($path) {
    if (strpos($path, 'http://') === 0) {
      $path = 'https://' . substr($path, 7);
    }
    return $path;
  }

  /**
   * Stable sort for CSS and JS items.
   *
   * Preserves the order of items with equal sort criteria.
   *
   * The function will sort by:
   * - $item['group'],      integer, ascending
   * - $item['weight'],     integer, ascending
   *
   * @param array &$assets
   *   Array of JS or CSS items, as in hook_alter_js() and hook_alter_css().
   *   The array keys can be integers or strings. The items are arrays.
   *
   * @see hook_alter_js()
   * @see hook_alter_css()
   */
  public static function sortStable(array &$assets) {
    $nested = [];
    foreach ($assets as $key => $item) {
      // Weight cast to string to preserve float.
      $weight = (string) $item['weight'];
      $nested[$item['group']][$weight][$key] = $item;
    }
    // First order by group, so that, for example, all items in the CSS_SYSTEM
    // group appear before items in the CSS_DEFAULT group, which appear before
    // all items in the CSS_THEME group. Modules may create additional groups by
    // defining their own constants.
    $sorted = [];
    // Sort group; then iterate over it.
    ksort($nested);
    foreach ($nested as &$group_items) {
      // Order by weight and iterate over it.
      ksort($group_items);
      foreach ($group_items as &$weight_items) {
        foreach ($weight_items as $key => &$item) {
          $sorted[$key] = $item;
        }
        unset($item);
      }
      unset($weight_items);
    }
    unset($group_items);
    $assets = $sorted;
  }

  /**
   * Generate an htaccess file in the optimized asset dirs to improve serving.
   *
   * @param string $extension
   *   The file type to use - either CSS or JS.
   * @param bool $regenerate
   *   Whether to regenerate if the file already exists.
   */
  public static function generateHtaccess($extension, $regenerate = FALSE) {
    $fileSystem = \Drupal::service('file_system');
    $path = "public://{$extension}/optimized";
    $file = $path . '/.htaccess';
    if (!$regenerate && file_exists($file)) {
      return;
    }
    /** @var \Drupal\Core\Config\Config $config */
    $config = \Drupal::config('advagg.settings');
    if ($extension === 'js') {
      $type = 'application/javascript';
    }
    else {
      $type = 'text/css';
    }
    $fileSystem->prepareDirectory($path, FileSystemInterface::CREATE_DIRECTORY);
    $immutable = ($config->get('immutable')) ? ', immutable' : '';
    $options = '';
    if ($config->get('symlinks')) {
      $options = 'Options +FollowSymlinks';
    }
    elseif ($config->get('symlinksifownermatch')) {
      $options = 'Options +SymLinksIfOwnerMatch';
    }
    $htaccess = <<<EOT
      {$options}
      <IfModule mod_rewrite.c>
        RewriteEngine on
        <IfModule mod_headers.c>
          # Serve brotli compressed {$extension} files if they exist and the client accepts br.
          RewriteCond %{HTTP:Accept-encoding} br
          RewriteCond %{REQUEST_FILENAME}\.br -s
          RewriteRule ^(.*)\.{$extension} $1\.{$extension}\.br [QSA]
          RewriteRule \.{$extension}\.br$ - [T={$type},E=no-gzip:1]

          <FilesMatch "\.{$extension}\.br$">
            # Serve correct encoding type.
            Header set Content-Encoding br
            # Force proxies to cache br/gzip/non-gzipped assets separately.
            Header append Vary Accept-Encoding
          </FilesMatch>

          # Serve gzip compressed {$extension} files if they exist and the client accepts gzip.
          RewriteCond %{HTTP:Accept-encoding} gzip
          RewriteCond %{REQUEST_FILENAME}\.gz -s
          RewriteRule ^(.*)\.{$extension} $1\.{$extension}\.gz [QSA]
          RewriteRule \.{$extension}\.gz$ - [T=application/javascript,E=no-gzip:1]

          <FilesMatch "\.{$extension}\.gz$">
            # Serve correct encoding type.
            Header set Content-Encoding gzip
            # Force proxies to cache br/gzip/non-gzipped assets separately.
            Header append Vary Accept-Encoding
          </FilesMatch>
        </IfModule>
      </IfModule>

      <FilesMatch "{$extension}(\.gz|\.br)?">
        # No mod_headers. Apache module headers is not enabled.
        <IfModule !mod_headers.c>
          # No mod_expires. Apache module expires is not enabled.
          <IfModule !mod_expires.c>
            # Use ETags.
            FileETag MTime Size
          </IfModule>
        </IfModule>

        # Use Expires Directive if apache module expires is enabled.
        <IfModule mod_expires.c>
          # Do not use ETags.
          FileETag None
          # Enable expirations.
          ExpiresActive On
          # Cache all aggregated {$extension} files for 52 weeks after access (A).
          ExpiresDefault A31449600
        </IfModule>

        # Use Headers Directive if apache module headers is enabled.
        <IfModule mod_headers.c>
          # Do not use etags for cache validation.
          Header unset ETag
          # Serve correct content type.
          Header set Content-Type {$type}
          <IfModule !mod_expires.c>
            # Set a far future Cache-Control header to 52 weeks.
            Header set Cache-Control "max-age=31449600, no-transform, public{$immutable}"
          </IfModule>
          <IfModule mod_expires.c>
            Header append Cache-Control "no-transform, public{$immutable}"
          </IfModule>
        </IfModule>
        ForceType {$type}
      </FilesMatch>
EOT;
    $fileSystem->saveData($htaccess, $file, FileSystemInterface::EXISTS_REPLACE);
  }

}
