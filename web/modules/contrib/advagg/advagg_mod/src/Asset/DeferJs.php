<?php

namespace Drupal\advagg_mod\Asset;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Component\Utility\Crypt;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Add defer tag to scripts.
 */
class DeferJs {

  /**
   * The defer type to use from advagg_mod configuration.
   *
   * @var int
   */
  protected $deferType;

  /**
   * The global count to use from advagg configuration.
   *
   * @var int
   */
  protected $counter;

  /**
   * The Drupal module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * A list of file cids to skip.
   *
   * @var array
   */
  protected $skipList;

  /**
   * DeferCss constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The Drupal module handler.
   */
  public function __construct(ConfigFactoryInterface $config_factory, ModuleHandlerInterface $module_handler) {
    $this->deferType = $config_factory->get('advagg_mod.settings')->get('css_defer_js_code');
    $this->counter = $config_factory->get('advagg.settings')->get('global_counter');
    $this->moduleHandler = $module_handler;
    $this->skipList = [];

    // Admin Toolbar 8x fails when deferred.
    if ($this->moduleHandler->moduleExists('admin_toolbar')) {
      $this->skipList[] = Crypt::hashBase64(drupal_get_path('module', 'admin_toolbar') . '/js/admin_toolbar.js' . $this->counter);
    }
  }

  /**
   * Add defer attribute to script tags.
   *
   * @param string $content
   *   The response content.
   *
   * @return string
   *   Updated content.
   */
  public function defer($content) {
    // Admin Toolbar 8x fails when deferred.
    foreach ($this->skipList as $cid) {
      if (strstr($content, $cid)) {
        return $content;
      }
    }

    // Only defer local scripts.
    if ($this->deferType === 2) {
      $pattern = '/<script src="\/[a-zA-Z0-0].*"/';
    }

    // Or defer all scripts.
    else {
      $pattern = '/<script src=".*"/';
    }
    return preg_replace_callback($pattern, [$this, 'callback'], $content);
  }

  /**
   * Callback to replace individual stylesheet links.
   *
   * @param array $matches
   *   Array from matches from preg_replace_callback.
   *
   * @return string
   *   Updated html string.
   */
  protected function callback(array $matches) {
    return "{$matches[0]} defer";
  }

}
