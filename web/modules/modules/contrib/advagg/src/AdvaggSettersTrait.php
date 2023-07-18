<?php

namespace Drupal\advagg;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Asset\AssetCollectionOptimizerInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Datetime\DateFormatInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\PrivateKey;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Theme\Registry;
use GuzzleHttp\ClientInterface;

/**
 * The trait setters of ADVAGG module.
 */
trait AdvaggSettersTrait {

  /**
   * The AdvAgg cache.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cache;

  /**
   * The CSS asset collection optimizer service.
   *
   * @var \Drupal\Core\Asset\AssetCollectionOptimizerInterface
   */
  protected $cssCollectionOptimizer;

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * The File System service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * Set File System service.
   *
   * @param \Drupal\Core\File\FileSystemInterface $fileSystem
   *   The File System service.
   *
   * @return $this
   */
  public function setFileSystem(FileSystemInterface $fileSystem) {
    $this->fileSystem = $fileSystem;
    return $this;
  }

  /**
   * The JavaScript asset collection optimizer service.
   *
   * @var \Drupal\Core\Asset\AssetCollectionOptimizerInterface
   */
  protected $jsCollectionOptimizer;

  /**
   * The core language manager service.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The Guzzle HTTP Client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * The private key service.
   *
   * @var \Drupal\Core\PrivateKey
   */
  protected $privateKey;

  /**
   * The Drupal renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * The theme registry service.
   *
   * @var \Drupal\Core\Theme\Registry
   */
  protected $themeRegistry;

  /**
   * Obtaining system time.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * The set cache backend.
   *
   * @param \Drupal\Core\Cache\CacheBackendInterface $cacheBackend
   *   The AdvAgg cache.
   *
   * @return $this
   *   This object.
   */
  public function setCache(CacheBackendInterface $cacheBackend) {
    $this->cache = $cacheBackend;
    return $this;
  }

  /**
   * Set CSS asset collection optimizer service.
   *
   * @param \Drupal\Core\Asset\AssetCollectionOptimizerInterface $cssCollectionOptimizer
   *   The CSS asset collection optimizer service.
   *
   * @return $this
   */
  public function setCssCollectionOptimizer(AssetCollectionOptimizerInterface $cssCollectionOptimizer) {
    $this->cssCollectionOptimizer = $cssCollectionOptimizer;
    return $this;
  }

  /**
   * Set date formatter service.
   *
   * @param \Drupal\Core\Datetime\DateFormatterInterface $dateFormatter
   *   The date formatter service.
   *
   * @return $this
   */
  public function setDateFomatter(DateFormatterInterface $dateFormatter) {
    $this->dateFormatter = $dateFormatter;
    return $this;
  }

  /**
   * Set http client.
   *
   * @param \GuzzleHttp\ClientInterface $httpClient
   *   The renderer.
   *
   * @return $this
   */
  public function setHttpClient(ClientInterface $httpClient) {
    $this->httpClient = $httpClient;
    return $this;
  }

  /**
   * Set JavaScript asset collection optimizer service.
   *
   * @param \Drupal\Core\Asset\AssetCollectionOptimizerInterface $jsCollectionOptimizer
   *   The JavaScript asset collection optimizer service.
   *
   * @return $this
   */
  public function setJsCollectionOptimizer(AssetCollectionOptimizerInterface $jsCollectionOptimizer) {
    $this->jsCollectionOptimizer = $jsCollectionOptimizer;
    return $this;
  }

  /**
   * Set language manager service.
   *
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   *   The core language manager service.
   *
   * @return $this
   */
  public function setLanguageManager(LanguageManagerInterface $languageManager) {
    $this->languageManager = $languageManager;
    return $this;
  }

  /**
   * Set module handler.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler service.
   *
   * @return $this
   */
  public function setModuleHandler(ModuleHandlerInterface $moduleHandler) {
    $this->moduleHandler = $moduleHandler;
    return $this;
  }

  /**
   * Set private key service.
   *
   * @param \Drupal\Core\PrivateKey $privateKey
   *   The private key service.
   *
   * @return $this
   */
  public function setPrivateKey(PrivateKey $privateKey) {
    $this->privateKey = $privateKey;
    return $this;
  }

  /**
   * Set renderer.
   *
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer.
   *
   * @return $this
   */
  public function setRenderer(RendererInterface $renderer) {
    $this->renderer = $renderer;
    return $this;
  }

  /**
   * Set state service.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   *
   * @return $this
   */
  public function setState(StateInterface $state) {
    $this->state = $state;
    return $this;
  }

  /**
   * The theme registry service.
   *
   * @param \Drupal\Core\Theme\Registry $themeRegistry
   *   The theme registry.
   *
   * @return $this
   */
  public function setThemeRegistry(Registry $themeRegistry) {
    $this->themeRegistry = $themeRegistry;
    return $this;
  }

  /**
   * Obtaining system time.
   *
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The system time.
   *
   * @return $this
   */
  public function setTime(TimeInterface $time) {
    $this->time = $time;
    return $this;
  }

}
