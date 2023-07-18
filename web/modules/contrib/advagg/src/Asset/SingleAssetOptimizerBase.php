<?php

namespace Drupal\advagg\Asset;

use Psr\Log\LoggerInterface;

/**
 * A base class for optimizing (especially minifying) assets.
 */
abstract class SingleAssetOptimizerBase {

  /**
   * Logger service.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * A config object for optimizer.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * Construct the optimizer.
   *
   * @param \Psr\Log\LoggerInterface $logger
   *   A PSR compatible logger.
   */
  public function __construct(LoggerInterface $logger) {
    $this->logger = $logger;
  }

  /**
   * Optimize the asset's content.
   *
   * @param string $content
   *   The content to optimize.
   * @param array $asset
   *   A core asset array.
   * @param array $data
   *   The cache data.
   *
   * @return string
   *   The optimized content.
   */
  abstract public function optimize($content, array $asset, array $data);

  /**
   * If configured, add licence string to top/bottom of file.
   *
   * @param string $contents
   *   The file contents.
   * @param string $path
   *   The original file path.
   */
  public function addLicense(&$contents, $path) {
    if ($this->config->get('add_license')) {
      $url = file_create_url($path);
      $contents = "/* Source and licensing information for the line(s) below can be found at $url. */\n" . $contents . "\n/* Source and licensing information for the above line(s) can be found at $url. */";
    }
  }

  /**
   * Checks if string contains multibyte characters.
   *
   * @param string $string
   *   String to check.
   *
   * @return bool
   *   TRUE if string contains multibyte character.
   */
  protected function stringContainsMultibyteCharacters($string) {
    // Check if there are multi-byte characters: If the UTF-8 encoded string has
    // multibyte chars strlen() will return a byte-count greater than the actual
    // character count, returned by drupal_strlen().
    if (strlen($string) === mb_strlen($string)) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Check if the asset is already minified.
   *
   * @param string $contents
   *   The asset contents.
   *
   * @return bool
   *   TRUE if the asset appears to be already minified.
   */
  protected function isMinified($contents) {
    // If a lot of semicolons probably already minified.
    $semicolon_count = substr_count($contents, ';');
    if ($semicolon_count > 10 && $semicolon_count > substr_count($contents, "\n")) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Check if minification was successful before saving changes.
   *
   * @param string $minified
   *   The minified asset contents.
   * @param string $original
   *   The original asset contents to compare against.
   *
   * @return bool
   *   TRUE for success.
   */
  protected function isMinificationSuccess($minified, $original) {
    // If the minified data is zero length it was a failure.
    if (empty($minified)) {
      return FALSE;
    }

    // If set, make sure the minified version doesn't have a suspiciously high
    // ratio or conversely a really low ratio.
    if (!$this->config->get('ratio_max')) {
      return TRUE;
    }
    $before = strlen($original);
    $after = strlen($minified);
    $ratio = !empty($before) ? (($before - $after) / $before) : 0;
    if ($ratio > $this->config->get('ratio_max') || $ratio < $this->config->get('ratio_min')) {
      return FALSE;
    }

    return TRUE;
  }

}
