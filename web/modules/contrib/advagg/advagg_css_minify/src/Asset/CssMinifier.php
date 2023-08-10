<?php

namespace Drupal\advagg_css_minify\Asset;

use Drupal\Component\Utility\Unicode;
use Drupal\advagg\Asset\SingleAssetOptimizerBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Psr\Log\LoggerInterface;
use tubalmartin\CssMin\Minifier as CSSmin;

/**
 * Optimizes a JavaScript asset.
 */
class CssMinifier extends SingleAssetOptimizerBase {

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
    $this->config = $config_factory->get('advagg_css_minify.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function optimize($contents, array $asset, array $data) {
    // Do nothing if css file minification is disabled.
    if (!$minifier = $this->config->get('minifier')) {
      return $contents;
    }

    // Do not re-minify if the file is already minified.
    if ($this->isMinified($contents)) {
      return $contents;
    }

    $contents = $this->clean($contents, $asset);
    $contents_original = $contents;

    // Do nothing if core minification is selected.
    if ($minifier === 1) {
      $contents = trim($this->minifyCore($contents));
    }
    else {
      $contents = trim($this->minifyCssMin($contents));
    }

    // If the contents are not empty, ensure that $data ends with ; or }.
    if (trim($contents) !== "" && strpbrk(substr(trim($contents), -1), ';})') === FALSE) {
      $contents .= ';';
    }

    if (!$this->isMinificationSuccess($contents, $contents_original)) {
      return $contents_original;
    };

    return $contents;
  }

  /**
   * Processes the contents of a CSS asset for cleanup.
   *
   * @param string $contents
   *   The contents of the CSS asset.
   * @param array $asset
   *   The core asset definition array.
   *
   * @return string
   *   Contents of the CSS asset.
   */
  protected function clean($contents, array $asset) {
    if ($encoding = Unicode::encodingFromBOM($contents)) {
      $contents = mb_substr(Unicode::convertToUtf8($contents, $encoding), 1);
    }

    // If no BOM is found, check for the charset attribute.
    elseif (isset($asset['attributes']['charset'])) {
      $contents = Unicode::convertToUtf8($contents, $asset['attributes']['charset']);
    }
    elseif (preg_match('/^@charset "([^"]+)";/', $contents, $matches)) {
      if ($matches[1] !== 'utf-8' && $matches[1] !== 'UTF-8') {
        $contents = substr($contents, strlen($matches[0]));
        $contents = Unicode::convertToUtf8($contents, $matches[1]);
      }
    }

    // Remove multiple charset declarations for standards compliance (and fixing
    // Safari problems).
    $contents = preg_replace('/^@charset\s+[\'"](\S*?)\b[\'"];/i', '', $contents);

    return $contents;
  }

  /**
   * Processes the contents of a stylesheet through CSSMin for minification.
   *
   * @param string $contents
   *   The contents of the stylesheet.
   *
   * @return string
   *   Minified contents of the stylesheet including the imported stylesheets.
   */
  protected function minifyCssMin($contents) {
    $cssmin = new CSSmin(TRUE);

    // Minify the CSS splitting lines after 4k of text.
    $contents = $cssmin->run($contents, 4096);

    // Replaces @import commands with the actual stylesheet content.
    // This happens recursively but omits external files.
    $contents = preg_replace_callback('/@import\s*(?:url\(\s*)?[\'"]?(?![a-z]+:)(?!\/\/)([^\'"\()]+)[\'"]?\s*\)?\s*;/', [$this, 'loadNestedFile'], $contents);

    return $contents;
  }

  /**
   * Minify a css string with the core css minification algorithm.
   *
   * @param string $contents
   *   The contents of the stylesheet.
   *
   * @see \Drupal\Core\Asset\CssOptimizer::processCss()
   *
   * @return string
   *   Minified css by the core minification method.
   */
  protected function minifyCore($contents) {
    // Perform some safe CSS optimizations.
    // Regexp to match comment blocks.
    $comment = '/\*[^*]*\*+(?:[^/*][^*]*\*+)*/';
    // Regexp to match double quoted strings.
    $double_quot = '"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"';
    // Regexp to match single quoted strings.
    $single_quot = "'[^'\\\\]*(?:\\\\.[^'\\\\]*)*'";
    // Strip all comment blocks, but keep double/single quoted strings.
    $contents = preg_replace(
      "<($double_quot|$single_quot)|$comment>Ss",
      "$1",
      $contents
    );
    // Remove certain whitespace.
    // There are different conditions for removing leading and trailing
    // whitespace.
    // @see http://php.net/manual/regexp.reference.subpatterns.php
    $contents = preg_replace('<
      # Do not strip any space from within single or double quotes
        (' . $double_quot . '|' . $single_quot . ')
      # Strip leading and trailing whitespace.
      | \s*([@{};,])\s*
      # Strip only leading whitespace from:
      # - Closing parenthesis: Retain "@media (bar) and foo".
      | \s+([\)])
      # Strip only trailing whitespace from:
      # - Opening parenthesis: Retain "@media (bar) and foo".
      # - Colon: Retain :pseudo-selectors.
      | ([\(:])\s+
    >xSs',
      // Only one of the four capturing groups will match, so its reference
      // will contain the wanted value and the references for the
      // two non-matching groups will be replaced with empty strings.
      '$1$2$3$4',
      $contents
    );

    return $contents;
  }

  /**
   * Loads stylesheets recursively and returns contents with corrected paths.
   *
   * This function is used for recursive loading of stylesheets and
   * returns the stylesheet content with all url() paths corrected.
   *
   * @param array $matches
   *   An array of matches files to load.
   *
   * @return string
   *   The contents of the CSS file at $matches[1], with corrected paths.
   *
   * @see \Drupal\Core\Asset\CssOptimizer::loadFile()
   */
  protected function loadNestedFile(array $matches) {
    $filename = $matches[1];
    // Load the imported stylesheet and replace @import commands in there as
    // well.
    $file = $this->loadFile($filename);

    // Determine the file's directory.
    $directory = dirname($filename);
    // If the file is in the current directory, make sure '.' doesn't appear in
    // the url() path.
    $directory = $directory == '.' ? '' : $directory . '/';

    // Alter all internal url() paths. Leave external paths alone. We don't need
    // to normalize absolute paths here because that will be done later. Also
    // ignore SVG paths (# or %23).
    return preg_replace('/url\(\s*([\'"]?)(?![a-z]+:|\/+|\#|\%23+)([^\'")]+)([\'"]?)\s*\)/i', 'url(\1' . $directory . '\2\3)', $file);
  }

  /**
   * Loads the stylesheet and resolves all @import commands.
   *
   * Loads a stylesheet and replaces @import commands with the contents of the
   * imported file. Use this instead of file_get_contents when processing
   * stylesheets.
   *
   * The returned contents are compressed removing white space and comments only
   * when CSS aggregation is enabled. This optimization will not apply for
   * color.module enabled themes with CSS aggregation turned off.
   *
   * Note: the only reason this method is public is so color.module can call it;
   * it is not on the AssetOptimizerInterface, so future refactorings can make
   * it protected.
   *
   * @param string $file
   *   Name of the stylesheet to be processed.
   *
   * @return string
   *   Contents of the stylesheet, including any resolved @import commands.
   */
  public function loadFile($file) {
    $content = '';
    if ($contents = @file_get_contents($file)) {
      $contents = $this->clean($contents, []);
      $content = $this->optimize($contents, ['data' => $file], []);
      $this->addLicense($contents, $file);
    }

    return $content;
  }

}
