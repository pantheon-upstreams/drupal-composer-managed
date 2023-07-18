<?php

/**
 * @file
 * Drush commands for Advanced CSS/JS Aggregation.
 */

use Drupal\Core\Cache\Cache;

/**
 * @defgroup advagg_drush Advanced Aggregates Drush Integration
 *
 * @{
 * Drush command integration for Advanced Aggregates.
 *
 * These are basically wrappers around the functions that can be called on the
 * Operations page.
 *
 * @see http://www.drush.org/en/master/
 */

/**
 * Implements hook_drush_help().
 */
function advagg_drush_help($command) {
  switch ($command) {
    case 'drush:advagg-da':
      return dt('Keep all Advagg settings but disable all functionality.');

    case 'drush:advagg-en':
      return dt('Restore all Advagg functionality if disabled.');

    case 'drush:advagg-cron':
      return dt('Run the advagg cron hook. This will clear out all stale advagg aggregated files, remove aggregates that include missing files, and remove unused aggregates.');

    case 'drush:advagg-clear-all-files':
      return dt('Remove all generated files. Useful if you think some of the generated files got corrupted and thus need to be deleted.');

    case 'drush:advagg-force-new-aggregates':
      /** @var \Drupal\Core\Config\Config $config */
      $config = \Drupal::service('config.factory')->get('advagg.settings');
      return dt('Force the creation of all new optimized files by incrementing a global counter. Current value of counter: %value. This is may be useful if a CDN has cached an something incorrectly as it will force new ones to be used even if nothing else has changed.', [
        '%value' => $config->get('global_counter'),
      ]);
  }
  return FALSE;
}

/**
 * Implements hook_drush_command().
 */
function advagg_drush_command() {
  $items = [];
  $items['advagg-cron'] = [
    'description' => dt('Run the advagg cron hook.'),
    'examples' => [
      'Standard example' => 'drush advagg-cron',
    ],
    'aliases' => ['advagg-c'],
  ];
  $items['advagg-disable'] = [
    'description' => dt('Disable Advagg.'),
    'examples' => [
      'Standard example' => 'drush advagg-disable',
    ],
    'aliases' => ['advagg-da'],
  ];
  $items['advagg-enable'] = [
    'description' => dt('Enable Advagg.'),
    'examples' => [
      'Standard example' => 'drush advagg-enable',
    ],
    'aliases' => ['advagg-en'],
  ];
  $items['advagg-clear-all-files'] = [
    'description' => dt('Remove all generated files.'),
    'examples' => [
      'Standard example' => 'drush advagg-clear-all-files',
    ],
    'aliases' => ['advagg-caf'],
  ];
  $items['advagg-force-new-aggregates'] = [
    'description' => dt('Force the creation of all new files by incrementing a global counter.'),
    'examples' => [
      'Standard example' => 'drush advagg-force-new-aggregates',
    ],
    'aliases' => ['advagg-fna'],
  ];
  return $items;
}

/**
 * Callback function for drush advagg-disable.
 */
function drush_advagg_disable() {
  /** @var \Drupal\Core\Config\Config $config */
  $config = \Drupal::service('config.factory')->getEditable('advagg.settings');
  $config->set('enabled', 0)->save();
  \Drupal::logger(dt('All Advagg functionality is disabled.'), 'ok');
}

/**
 * Callback function for drush advagg-enable.
 */
function drush_advagg_enable() {
  /** @var \Drupal\Core\Config\Config $config */
  $config = \Drupal::service('config.factory')->getEditable('advagg.settings');
  $config->set('enabled', 1)->save();
  \Drupal::logger(dt('All Advagg functionality is enabled.'), 'ok');
}

/**
 * Callback function for drush advagg-force-new-aggregates.
 */
function drush_advagg_force_new_aggregates() {
  // Clear out the cache.
  drush_advagg_clear_all_files();

  // Increment counter.
  /** @var \Drupal\Core\Config\Config $config */
  $config = \Drupal::service('config.factory')->getEditable('advagg.settings');
  $new_value = $config->get('global_counter') + 1;
  $config->set('global_counter', $new_value)->save();
  \Drupal::logger(dt('Global counter is now set to @new_value', ['@new_value' => $new_value]), 'ok');
  _drupal_flush_css_js();
}

/**
 * Callback function for drush advagg-clear-all-files.
 */
function drush_advagg_clear_all_files() {
  $file_system = \Drupal::service('file_system');
  // Clear out the cache.
  Cache::invalidateTags(['library_info']);
  \Drupal::cache('advagg')->invalidateAll();
  $pub = $file_system->realpath('public://');
  $css_count = count(glob($pub . '/css/optimized/*.css'));
  $js_count = count(glob($pub . '/js/optimized/*.js'));
  foreach (['public://js/optimized', 'public://css/optimized'] as $path) {
    if (file_exists($path)) {
      $file_system->deleteRecursive($path);
    }
  }

  // Report back the results.
  \Drupal::logger(dt('All AdvAgg optimized files have been deleted. %css_count CSS files and %js_count JS files have been removed.', [
    '%css_count' => $css_count,
    '%js_count' => $js_count,
  ]));
}

/**
 * Callback function for drush advagg-cron.
 */
function drush_advagg_cron() {
  // Run AdvAgg cron job.
  $output = advagg_cron(TRUE);

  if (!empty($output['css']) || !empty($output['js'])) {
    \Drupal::logger(dt('All stale aggregates have been deleted. %css_count CSS files and %js_count JS files have been removed.', [
      '%css_count' => count($output['css']),
      '%js_count' => count($output['js']),
    ]), 'ok');
  }
  else {
    \Drupal::logger(dt('No stale aggregates found. Nothing was deleted.'), 'ok');
  }
}

/**
 * @} End of "defgroup advagg_drush".
 */
