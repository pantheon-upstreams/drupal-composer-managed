<?php

namespace Drupal\advagg_bundler\Asset;

use Drupal\Core\Asset\AssetCollectionGrouperInterface;
use Drupal\Core\Asset\JsCollectionGrouper;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Groups JavaScript assets.
 */
class AdvaggJsCollectionGrouper extends JsCollectionGrouper implements AssetCollectionGrouperInterface {

  /**
   * Construct the grouper instance.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   A config factory for retrieving required config objects.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->config = $config_factory->get('advagg_bundler.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function group(array $js_assets) {
    $max = $this->config->get('js.max');

    // Only modify core grouping if bundler is enabled.
    if (!$this->config->get('active') || !$max) {
      return parent::group($js_assets);
    }

    $logic = $this->config->get('js.logic');
    $preprocess_count = count(array_filter(array_column($js_assets, 'preprocess')));
    $target = max($max - (count($js_assets) - $preprocess_count), 1);
    if ($logic === 0) {
      $split = round($preprocess_count / $target);
    }
    else {
      $split = array_sum(array_column($js_assets, 'size')) / $target;
    }

    $groups = [];

    // If a group can contain multiple items, we track the information that must
    // be the same for each item in the group, so that when we iterate the next
    // item, we can determine if it can be put into the current group, or if a
    // new group needs to be made for it.
    $current_group_keys = NULL;
    $current_size = 0;
    $index = -1;
    foreach ($js_assets as $item) {
      // The browsers for which the JavaScript item needs to be loaded is part
      // of the information that determines when a new group is needed, but the
      // order of keys in the array doesn't matter, and we don't want a new
      // group if all that's different is that order.
      ksort($item['browsers']);

      switch ($item['type']) {
        case 'file':
          // Group file items if their 'preprocess' flag is TRUE.
          // Help ensure maximum reuse of aggregate files by only grouping
          // together items that share the same 'group' value.
          $arrayItems = [$item['type'], $item['group'], $item['browsers']];
          $group_keys = $item['preprocess'] ? $arrayItems : FALSE;
          break;

        case 'external':
          // Do not group external items.
          $group_keys = FALSE;
          break;
      }

      // If the group keys don't match the most recent group we're working with,
      // then a new group must be made.
      if ($group_keys !== $current_group_keys) {
        $index++;
        // Initialize the new group with the same properties as the first item
        // being placed into it. The item's 'data' and 'weight' properties are
        // unique to the item and should not be carried over to the group.
        $groups[$index] = $item;
        unset($groups[$index]['data'], $groups[$index]['weight']);
        $groups[$index]['items'] = [];
        $current_group_keys = $group_keys ? $group_keys : NULL;
      }

      // Add the item to the current group.
      $groups[$index]['items'][] = $item;

      if ($current_group_keys) {
        if ($logic === 0) {
          if (count($groups[$index]['items']) >= $split) {
            $current_group_keys = NULL;
          }
        }
        else {
          $current_size += isset($item['size']) ? $item['size'] : 0;
          if ($current_size >= $split) {
            $current_size = 0;
            $current_group_keys = NULL;
          }
        }
      }
      else {
        $current_size = 0;
      }
    }

    return $groups;
  }

}
