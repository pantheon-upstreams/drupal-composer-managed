<?php

/**
 * Load services definition file.
 */
$settings['container_yamls'][] = __DIR__ . '/services.yml';

/**
 * Include the Pantheon-specific settings file.
 *
 * n.b. The settings.pantheon.php file makes some changes
 *      that affect all environments that this site
 *      exists in.  Always include this file, even in
 *      a local development environment, to ensure that
 *      the site settings remain consistent.
 */
include __DIR__ . "/settings.pantheon.php";

/**
 * Environment specific settings to enable Storybook and CL Server integration.
 */
if (
  isset($_ENV['PANTHEON_ENVIRONMENT']) &&
  (($_ENV['PANTHEON_ENVIRONMENT'] == 'dev') || ($_ENV['PANTHEON_ENVIRONMENT'] == 'components'))
) {
  /* Disable CSS and JS aggregation for storybook sake. */
  $config['system.performance']['css']['preprocess'] = FALSE;
  $config['system.performance']['js']['preprocess'] = FALSE;

  /* Enable anonymous access to CL Server */
  $config['user.role.anonymous']['permissions'][] = 'use cl server';
}

/**
 * Configuration for local solr search
 */
if (isset($_ENV['PANTHEON_ENVIRONMENT']) && $_ENV['PANTHEON_ENVIRONMENT'] === 'lando') {
  $config['search_api.index.content_index']['server'] = 'local';
  $config['search_api.server.pantheon_solr8']['status'] = false;
} else {
  $config['search_api.server.local']['status'] = false;
}

/**
 * Skipping permissions hardening will make scaffolding
 * work better, but will also raise a warning when you
 * install Drupal.
 *
 * https://www.drupal.org/project/drupal/issues/3091285
 */
// $settings['skip_permissions_hardening'] = TRUE;

$lando_settings = __DIR__ . "/settings.lando.php";
if (file_exists($lando_settings)) {
  include $lando_settings;
}

/**
 * If there is a local settings file, then include it
 */
$local_settings = __DIR__ . "/settings.local.php";
if (file_exists($local_settings)) {
  include $local_settings;
}
