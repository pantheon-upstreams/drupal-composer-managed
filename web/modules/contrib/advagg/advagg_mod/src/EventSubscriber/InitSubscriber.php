<?php

namespace Drupal\advagg_mod\EventSubscriber;

use Drupal\advagg\Asset\AssetOptimizationEvent;
use Drupal\advagg_mod\Asset\AsyncJs;
use Drupal\advagg_mod\Asset\DeferCss;
use Drupal\advagg_mod\Asset\DeferJs;
use Drupal\advagg_mod\Asset\RemoveConsoleLog;
use Drupal\advagg_mod\Asset\TranslateCss;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Render\HtmlResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Perform initialization tasks for advagg_mod.
 */
class InitSubscriber implements EventSubscriberInterface {

  /**
   * A config object for the advagg_mod configuration.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * A config object for the advagg configuration.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $advaggConfig;

  /**
   * The CSS translator service.
   *
   * @var \Drupal\advagg_mod\Asset\TranslateCss
   */
  protected $translator;

  /**
   * The CSS defer service.
   *
   * @var \Drupal\advagg_mod\Asset\DeferCss
   */
  protected $cssDeferer;

  /**
   * The JS defer service.
   *
   * @var \Drupal\advagg_mod\Asset\DeferJs
   */
  protected $jsDeferer;

  /**
   * The JS asyncer service.
   *
   * @var \Drupal\advagg_mod\Asset\AsyncJs
   */
  protected $jsAsyncer;

  /**
   * The Console.log remover.
   *
   * @var \Drupal\advagg_mod\Asset\RemoveConsoleLog
   */
  protected $consoleLogRemover;

  /**
   * The file system.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * Constructs the Subscriber object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   A config factory for retrieving required config objects.
   * @param \Drupal\advagg_mod\Asset\TranslateCss $translator
   *   The translator service.
   * @param \Drupal\advagg_mod\Asset\DeferCss $deferer
   *   The CSS deferer.
   * @param \Drupal\Advagg_mod\Asset\AsyncJs $js_asyncer
   *   The JS asyncer.
   * @param \Drupal\advagg_mod\Asset\DeferJs $js_deferer
   *   The JS deferer.
   * @param \Drupal\Advagg_mod\Asset\RemoveConsoleLog $js_console_log
   *   The class to remove console.log() calls.
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   The file system.
   */
  public function __construct(ConfigFactoryInterface $config_factory, TranslateCss $translator, DeferCss $deferer, AsyncJs $js_asyncer, DeferJs $js_deferer, RemoveConsoleLog $js_console_log, FileSystemInterface $file_system) {
    $this->config = $config_factory->getEditable('advagg_mod.settings');
    $this->advaggConfig = $config_factory->getEditable('advagg.settings');
    $this->translator = $translator;
    $this->cssDeferer = $deferer;
    $this->jsAsyncer = $js_asyncer;
    $this->jsDeferer = $js_deferer;
    $this->consoleLogRemover = $js_console_log;
    $this->fileSystem = $file_system;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      KernelEvents::REQUEST => ['onEvent', 0],
      KernelEvents::RESPONSE => [
        ['deferCss', 0],
        ['deferJs', 0],
        ['asyncJs', 0],
      ],
      AssetOptimizationEvent::CSS => ['translateCss', 0],
      AssetOptimizationEvent::JS => ['removeConsoleLog', -10],
    ];
  }

  /**
   * Synchronize global_counter variable between sites.
   *
   * Only if using unified_multisite_dir.
   */
  public function onEvent() {
    $dir = rtrim($this->config->get('unified_multisite_dir'), '/');
    if (empty($dir) || !file_exists($dir) || !is_dir($dir)) {
      return;
    }

    $counter_filename = $dir . '/_global_counter';
    $local_counter = $this->advaggConfig->get('global_counter');
    if (!file_exists($counter_filename)) {
      $this->fileSystem->saveData($local_counter, $counter_filename, FileSystemInterface::EXISTS_REPLACE);
    }
    else {
      $shared_counter = (int) file_get_contents($counter_filename);

      if ($shared_counter == $local_counter) {
        // Counters are the same, return.
        return;
      }
      elseif ($shared_counter < $local_counter) {
        // Local counter is higher, update saved file and return.
        $this->fileSystem->saveData($local_counter, $counter_filename, FileSystemInterface::EXISTS_REPLACE);
        return;
      }
      elseif ($shared_counter > $local_counter) {
        // Shared counter is higher, update local copy and return.
        $this->advaggConfig->set('global_counter', $shared_counter)->save();
        return;
      }
    }
  }

  /**
   * Pass the CSS to the translator.
   *
   * @param \Drupal\advagg\Asset\AssetOptimizationEvent $assetOptimizationEvent
   *   The CSS optimization event.
   */
  public function translateCss(AssetOptimizationEvent $assetOptimizationEvent) {
    // Skip if not enabled.
    if (!$this->config->get('css_translate')) {
      return;
    }
    $content = $assetOptimizationEvent->getContent();
    $content = $this->translator->optimize($content, [], []);
    $assetOptimizationEvent->setContent($content);
  }

  /**
   * Pass the JS to the modifier if enabled to remove console logging.
   *
   * @param \Drupal\advagg\Asset\AssetOptimizationEvent $assetOptimizationEvent
   *   The JS optimization event.
   */
  public function removeConsoleLog(AssetOptimizationEvent $assetOptimizationEvent) {
    // Skip if not enabled.
    if (!$this->config->get('js_remove_console_log')) {
      return;
    }
    $content = $assetOptimizationEvent->getContent();
    $content = $this->consoleLogRemover->optimize($content);
    $assetOptimizationEvent->setContent($content);
  }

  /**
   * Apply CSS defer actions.
   *
   * @param \Symfony\Component\HttpKernel\Event\ResponseEvent $event
   *   The response event object.
   */
  public function deferCss(ResponseEvent $event) {
    // Skip if not enabled.
    if (!advagg_mod_css_defer_active()) {
      return;
    }
    $response = $event->getResponse();

    // Only process Html Responses.
    if (!$response instanceof HtmlResponse) {
      return;
    }
    $content = $this->cssDeferer->defer($response->getContent());
    $response->setContent($content);

  }

  /**
   * Apply defer JS changes.
   *
   * @param \Symfony\Component\HttpKernel\Event\ResponseEvent $event
   *   The response event object.
   */
  public function deferJs(ResponseEvent $event) {
    // Skip if not enabled.
    if (!$this->config->get('js_defer')) {
      return;
    }

    $response = $event->getResponse();

    // Only process Html Responses.
    if (!$response instanceof HtmlResponse) {
      return;
    }
    $content = $this->jsDeferer->defer($response->getContent());
    $response->setContent($content);

  }

  /**
   * Apply CSS defer actions.
   *
   * @param \Symfony\Component\HttpKernel\Event\ResponseEvent $event
   *   The response event object.
   */
  public function asyncJs(ResponseEvent $event) {
    // Skip if not enabled.
    if (!$this->config->get('js_async') || $this->config->get('js_defer')) {
      return;
    }
    $response = $event->getResponse();

    // Only process Html Responses.
    if (!$response instanceof HtmlResponse) {
      return;
    }
    $content = $this->jsAsyncer->async($response->getContent());
    $response->setContent($content);
  }

}
