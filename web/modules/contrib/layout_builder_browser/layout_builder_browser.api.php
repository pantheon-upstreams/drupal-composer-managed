<?php

/**
 * @file
 * Hooks for the layout_builder_browser module.
 */

/**
 * @addtogroup hooks
 * @{
 */


/**
 * Alter the browser render array.
 *
 * @param array $build
 *   The render array to alter.
 * @param array context
 *   Contextual information like the section storage, delta and region.
 */
function hook_layout_builder_browser_alter(array &$build, array $context) {
  // Alter the placeholder of the search textfield.
  $build['filter']['#placeholder'] = t('Block name');

  // Collapse the categories other than "Common".
  foreach ($build['block_categories'] as $category_key => &$category) {
    if ($category_key === 'common' || !isset($category['#type']) || $category['#type'] !== 'details') {
      continue;
    }

    $category['#open'] = FALSE;
  }
}

/**
 * @} End of "addtogroup hooks".
 */
