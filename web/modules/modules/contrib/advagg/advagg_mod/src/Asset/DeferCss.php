<?php

namespace Drupal\advagg_mod\Asset;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Component\Utility\Crypt;

/**
 * Modify stylesheet links to defer them. May lead to Flash of unstyled content.
 */
class DeferCss {

  /**
   * The defer method to use from advagg_mod configuration.
   *
   * @var int
   */
  protected $deferMethod;

  /**
   * The global counter to use for calculating paths.
   *
   * @var int
   */
  protected $counter;

  /**
   * Whether or not to alter external stylesheets.
   *
   * @var bool
   */
  protected $external;

  /**
   * DeferCss constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->deferMethod = $config_factory->get('advagg_mod.settings')->get('css_defer_js_code');
    $this->counter = $config_factory->get('advagg.settings')->get('global_counter');
    $this->external = $config_factory->get('advagg_mod.settings')->get('css_defer_external');
  }

  /**
   * Replace stylesheet links with preload & noscript links.
   *
   * @param string $content
   *   The response content.
   *
   * @return string
   *   Updated content.
   */
  public function defer($content) {
    if ($this->external) {
      $pattern = '/<link rel=["\']stylesheet["\'](.*)(href="[a-zA-Z0-9\/_\.\-\?\:]*")(.*)\/\>/';
    }
    else {
      $pattern = '/<link rel=["\']stylesheet["\'](.*)(href="\/[a-zA-Z0-9][a-zA-Z0-9\/_\.\-\?]*")(.*)\/\>/';
    }

    $content = preg_replace_callback($pattern, [$this, 'callback'], $content);

    // Put JS inline if configured.
    if ($this->deferMethod === 0) {
      $path = drupal_get_path('module', 'advagg_mod') . '/js/loadCSS.js';
      if (!strpos($content, $path)) {
        $path = Crypt::hashBase64($path . $this->counter);
      }
      else {
        $path = str_replace('/', '\/', $path);
      }
      $path = preg_quote($path, '/');
      $pattern = "/<script src=['\"]\/(.*{$path}.*)\?.*['\"]>/";
      $content = preg_replace_callback($pattern, [$this, 'inlineScript'], $content);
    }
    return $content;
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
    return "<link rel='preload' {$matches[1]} {$matches[2]} as='style' onload=\"this.rel='stylesheet'\" {$matches[3]}/><noscript>{$matches[0]}</noscript>";
  }

  /**
   * Callback to replace the script link with an inline script.
   *
   * @param array $matches
   *   Array from matches from preg_replace_callback.
   *
   * @return string
   *   Updated html string.
   */
  protected function inlineScript(array $matches) {
    $data = @file_get_contents($matches[1]);
    return "<script>{$data}";
  }

}
