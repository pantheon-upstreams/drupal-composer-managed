<?php

namespace Drupal\advagg_mod\Asset;

/**
 * Add async tag to scripts.
 */
class AsyncJs {

  /**
   * Add Async attribute to all external script tags.
   *
   * @param string $content
   *   The response content.
   *
   * @return string
   *   Updated content.
   */
  public function async($content) {
    $pattern = '/<script src=".*"/';
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
    return "{$matches[0]} async";
  }

}
