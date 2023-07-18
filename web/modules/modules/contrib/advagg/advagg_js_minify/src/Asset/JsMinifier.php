<?php

namespace Drupal\advagg_js_minify\Asset;

use Drupal\Component\Utility\Unicode;
use Drupal\advagg\Asset\SingleAssetOptimizerBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Psr\Log\LoggerInterface;

/**
 * Optimizes a JavaScript asset.
 */
class JsMinifier extends SingleAssetOptimizerBase {

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
    $this->config = $config_factory->get('advagg_js_minify.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function optimize($contents, array $asset, array $data) {
    // Do nothing if js file minification is disabled.
    if (!$minifier = $this->config->get('minifier')) {
      return $contents;
    }

    // Do not re-minify if the file is already minified.
    if ($this->isMinified($contents)) {
      return $contents;
    }
    $this->clean($contents, $asset);

    $contents_original = $contents;

    $function = $this->getFunction($minifier);
    if (!is_callable($function)) {
      return $contents;
    }

    $arguments = [&$contents, $asset['data']];
    call_user_func_array($function, $arguments);

    $contents = trim($contents);

    // Ensure that $data ends with ; or }.
    if (strpbrk(substr(trim($contents), -1), ';})') === FALSE) {
      $contents .= ';';
    }

    if (!$this->isMinificationSuccess($contents, $contents_original)) {
      return $contents_original;
    };

    return $contents;
  }

  /**
   * Get the function to use.
   *
   * @param int $minifier
   *   Configuration value.
   *
   * @return callable
   *   the callable to minify the contents.
   */
  protected function getFunction($minifier) {
    $functions = [
      1 => [$this, 'minifyJsminplus'],
      2 => [$this, 'minifyJspacker'],
      3 => [$this, 'minifyJsmin'],
      4 => [$this, 'minifyJshrink'],
      5 => [$this, 'minifyJsqueeze'],
    ];
    return $functions[$minifier];
  }

  /**
   * Processes the contents of a javascript asset for cleanup.
   *
   * @param string $contents
   *   The contents of the javascript asset.
   * @param array $asset
   *   The core asset definition array.
   *
   * @return string
   *   Contents of the javascript asset.
   */
  public function clean($contents, array $asset) {
    if ($encoding = Unicode::encodingFromBOM($contents)) {
      $contents = mb_substr(Unicode::convertToUtf8($contents, $encoding), 1);
    }

    // If no BOM is found, check for the charset attribute.
    elseif (isset($asset['attributes']['charset'])) {
      $contents = Unicode::convertToUtf8($contents, $asset['attributes']['charset']);
    }

    // Remove JS source and source mapping urls or these may cause 404 errors.
    $contents = preg_replace('/\/\/(#|@)\s(sourceURL|sourceMappingURL)=\s*(\S*?)\s*$/m', '', $contents);

    return $contents;
  }

  /**
   * Minify a JS string using jsmin.
   *
   * @param string $contents
   *   JavaScript string.
   * @param string $path
   *   The original file path.
   */
  public function minifyJsmin(&$contents, $path) {
    // Do not use jsmin() if the function can not be called.
    if (!function_exists('jsmin')) {
      $this->logger->notice($this->t('The jsmin function does not exist. Using JSqueeze.'), []);
      $contents = $this->minifyJsqueeze($contents, $path);
      return;
    }

    // Jsmin doesn't handle multi-byte characters before version 2, fall back to
    // different minifier if jsmin version < 2 and $contents contains multi-
    // byte characters.
    if (version_compare(phpversion('jsmin'), '2.0.0', '<') && $this->stringContainsMultibyteCharacters($contents)) {
      $this->logger->notice('The currently installed jsmin version does not handle multibyte characters, you may consider to upgrade the jsmin extension. Using JSqueeze fallback.', []);
      $contents = $this->minifyJsqueeze($contents, $path);
      return;
    }

    // Jsmin may have errors (incorrectly determining EOLs) with mixed tabs
    // and spaces. An example: jQuery.Cycle 3.0.3 - http://jquery.malsup.com/
    $contents = str_replace("\t", " ", $contents);

    $minified = jsmin($contents);

    // Check for JSMin errors.
    $error = jsmin_last_error_msg();
    if ($error != 'No error') {
      $this->logger->warning('JSMin had an error processing, using JSqueeze fallback. Error details: ' . $error, []);
      $contents = $this->minifyJsqueeze($contents, $path);
      return;
    }

    // Under some unknown/rare circumstances, JSMin can add up to 5
    // extraneous/wrong chars at the end of the string. Check and remove if
    // necessary. The chars unfortunately vary in number and specific chars.
    // Hence this is a poor quality check but often works.
    if (ctype_cntrl(substr(trim($minified), -1)) || strpbrk(substr(trim($minified), -1), ';})') === FALSE) {
      $this->logger->notice($this->t('JSMin had a possible error minifying: @file, correcting.', ['@file' => $path]));
      if (strrpos(substr($minified, -10), ';')) {
        $contents = substr($minified, 0, strrpos($minified, ';'));
      }
    }
    else {
      $contents = $minified;
    }
    $semicolons = substr_count($contents, ';', strlen($contents) - 5);
    if ($semicolons > 2) {
      $start = substr($contents, 0, -5);
      $contents = $start . preg_replace("/([;)}]*)([\w]*)([;)}]*)/", "$1$3", substr($contents, -5));
      $this->logger->notice($this->t('JSMin had an error minifying file: @file, attempting to correct.', ['@file' => $path]));
    }
  }

  /**
   * Minify a JS string using jsmin+.
   *
   * @param string $contents
   *   Javascript string.
   */
  public function minifyJsminplus(&$contents, $path) {
    $contents_before = $contents;

    // Only include jsminplus.inc if the JSMinPlus class doesn't exist.
    if (!class_exists('\JSMinPlus')) {
      include drupal_get_path('module', 'advagg_js_minify') . '/jsminplus.inc';
      $nesting_level = ini_get('xdebug.max_nesting_level');
      if (!empty($nesting_level) && $nesting_level < 200) {
        ini_set('xdebug.max_nesting_level', 200);
      }
    }
    ob_start();
    try {
      // JSMin+ the contents of the aggregated file.
      $contents = \JSMinPlus::minify($contents);

      // Capture any output from JSMinPlus.
      $error = trim(ob_get_contents());
      if (!empty($error)) {
        throw new \Exception($error);
      }
    }
    catch (\Exception $e) {
      // Log exception thrown by JSMin+ and roll back to uncompressed content.
      $this->logger->warning('JSMinPlus had a possible error minifying: @file. Using uncompressed version. Error: ' . $e->getMessage(), ['@file' => $path]);
      $contents = $contents_before;
    }
    ob_end_clean();
  }

  /**
   * Minify a JS string using packer.
   *
   * @param string $contents
   *   Javascript string.
   */
  public function minifyJspacker(&$contents) {
    // Use Packer on the contents of the aggregated file.
    if (!class_exists('\JavaScriptPacker')) {
      include drupal_get_path('module', 'advagg_js_minify') . '/jspacker.inc';
    }

    // Add semicolons to the end of lines if missing.
    $contents = str_replace("}\n", "};\n", $contents);
    $contents = str_replace("\nfunction", ";\nfunction", $contents);

    $packer = new \JavaScriptPacker($contents, 62, TRUE, FALSE);
    $contents = $packer->pack();
  }

  /**
   * Minify a JS string using jshrink.
   *
   * @param string $contents
   *   Javascript string.
   */
  public function minifyJshrink(&$contents, $path) {
    $contents_before = $contents;

    // Only include jshrink.inc if the JShrink\Minifier class doesn't exist.
    if (!class_exists('\JShrink\Minifier')) {
      include drupal_get_path('module', 'advagg_js_minify') . '/jshrink.inc';
      $nesting_level = ini_get('xdebug.max_nesting_level');
      if (!empty($nesting_level) && $nesting_level < 200) {
        ini_set('xdebug.max_nesting_level', 200);
      }
    }
    ob_start();
    try {
      // JShrink the contents of the aggregated file.
      // @codingStandardsIgnoreLine
      $contents = \JShrink\Minifier::minify($contents, ['flaggedComments' => FALSE]);

      // Capture any output from JShrink.
      $error = trim(ob_get_contents());
      if (!empty($error)) {
        throw new \Exception($error);
      }
    }
    catch (\Exception $e) {
      // Log the JShrink exception and rollback to uncompressed content.
      $this->logger->warning('JShrink had a possible error minifying: @file. Using uncompressed version. Error: ' . $e->getMessage(), ['@file' => $path]);
      $contents = $contents_before;
    }
    ob_end_clean();
  }

  /**
   * Minify a JS string using jsqueeze.
   *
   * @param string $contents
   *   Javascript string.
   */
  public function minifyJsqueeze(&$contents, $path = NULL) {
    $contents_before = $contents;

    // Only include jshrink.inc if the Patchwork\JSqueeze class doesn't exist.
    if (!class_exists('\Patchwork\JSqueeze')) {
      include drupal_get_path('module', 'advagg_js_minify') . '/jsqueeze.inc';
      $nesting_level = ini_get('xdebug.max_nesting_level');
      if (!empty($nesting_level) && $nesting_level < 200) {
        ini_set('xdebug.max_nesting_level', 200);
      }
    }
    ob_start();
    try {
      // Minify the contents of the aggregated file.
      // @codingStandardsIgnoreLine
      $jz = new \Patchwork\JSqueeze();
      $contents = $jz->squeeze(
        $contents,
        TRUE,
        $this->config->get('add_license'),
        FALSE
      );

      // Capture any output from JSqueeze.
      $error = trim(ob_get_contents());
      if (!empty($error)) {
        throw new \Exception($error);
      }
    }
    catch (\Exception $e) {
      // Log the JSqueeze exception and rollback to uncompressed content.
      $this->logger->warning('JSqueeze had a possible error minifying: @file. Using uncompressed version. Error: ' . $e->getMessage(), ['@file' => $path]);
      $contents = $contents_before;
    }
    ob_end_clean();
  }

}
