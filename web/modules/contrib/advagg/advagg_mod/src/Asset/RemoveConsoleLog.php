<?php

namespace Drupal\advagg_mod\Asset;

/**
 * Remove console.log() calls from JavaScript Files.
 */
class RemoveConsoleLog {

  /**
   * Scan asset contents for console.log() calls and remove them.
   *
   * @param string $contents
   *   The asset contents.
   *
   * @return string
   *   The updated contents.
   */
  public function optimize($contents) {
    $pattern = "/ console.log(.*)/";
    return preg_replace_callback($pattern, [$this, 'removeCallback'], $contents);
  }

  /**
   * Remove the console.log() call.
   *
   * @param array $matches
   *   Array of matches from preg_replace_callback().
   *
   * @return string
   *   Replaced string.
   */
  protected function removeCallback(array $matches) {
    return '';
  }

}
