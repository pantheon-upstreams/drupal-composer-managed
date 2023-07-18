<?php

namespace Drupal\hearsay_trailing_slash\PathProcessor;

use Drupal\Core\PathProcessor\OutboundPathProcessorInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TrailingSlashPathProcessor
 *
 * @package Drupal\hearsay_trailing_slash\PathProcessor
 */
class TrailingSlashPathProcessor implements OutboundPathProcessorInterface
{
    /**
     * Add trailing slash for everybody paths.
     *
     * @param string $path
     *   Path of the Page.
     * @param array $options
     *   Required options for Path.
     * @param \Symfony\Component\HttpFoundation\Request|null $request
     *   Request Stack.
     * @param \Drupal\Core\Render\BubbleableMetadata|null $bubbleable_metadata
     *   Bubbleable Metadata.
     *
     * @return string|string[]|null
     *   Return URL or Null.
     */
    function processOutbound($path, &$options = array(), Request $request = NULL, BubbleableMetadata $bubbleable_metadata = NULL)
    {
        // Skip trailing slash for front page and admin pages.
        if ($path == '/' || empty($path) || (bool) strstr($path, '/admin')) {
            return $path;
        }
        // Skip for transliterate url
        if ((bool) strstr($path, '/machine_name/transliterate')) {
            return $path;
        }
        // Skip for user reset url.
        if ((bool) strstr($path, '/user/reset')) {
            return $path;
        }
        return preg_replace('/((?:^|\\/)[^\\/\\.]+?)$/isD', '$1', $path);
    }
}
