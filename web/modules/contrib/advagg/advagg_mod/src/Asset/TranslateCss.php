<?php

namespace Drupal\advagg_mod\Asset;

use Drupal\advagg\Asset\SingleAssetOptimizerBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Psr\Log\LoggerInterface;

/**
 * Applies the t() function to strings in CSS assets.
 */
class TranslateCss extends SingleAssetOptimizerBase {

  use StringTranslationTrait;

  /**
   * Construct the optimizer instance.
   *
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   A config factory for retrieving required config objects.
   */
  public function __construct(LoggerInterface $logger, ConfigFactoryInterface $config_factory) {
    parent::__construct($logger);
    $this->config = $config_factory->get('advagg_mod.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function optimize($contents, array $asset, array $data) {
    // Code taken from \Drupal\Core\Asset\CssOptimizer::processCss().
    // Regexp to match double quoted strings.
    $double_quot = '"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"';
    // Regexp to match single quoted strings.
    $single_quot = "'[^'\\\\]*(?:\\\\.[^'\\\\]*)*'";
    // Extract all content inside of quotes.
    $pattern = "/content:.*?($double_quot|$single_quot|(?:\\;|\\})).*?(?:\\;|\\})/";

    // Run strings inside of quotes of the content attribute through the t
    // function.
    return preg_replace_callback($pattern, [$this, 'translateCallback'], $contents);
  }

  /**
   * Run preg matches through the t() function.
   *
   * @param array $matches
   *   Array of matches from preg_replace_callback().
   *
   * @return string
   *   Replaced string.
   */
  protected function translateCallback(array $matches) {
    // Skip if equal to ; or }.
    if ($matches[1] === ';' || $matches[1] === '}') {
      return $matches[0];
    }

    // Remove quotes for t function.
    $before = substr($matches[1], 1, -1);

    // Only run if it contains A-Za-z.
    if (!preg_match('/[A-Za-z]/', $before)) {
      return $matches[0];
    }

    // Only run if it contains characters other than unicode.
    $css_unicode_pattern = '/\\\\[0-9a-fA-F]{1,6}(?:\\r\\n|[ \\t\\r\\n\\f])?/';
    $unicode_removed = preg_replace($css_unicode_pattern, '', $before);
    if (empty($unicode_removed)) {
      return $matches[0];
    }

    // Run t function and put back into string.
    return str_replace($before, (string) $this->t('@before', ['@before' => $before]), $matches[0]);
  }

}
