<?php
/**
 * Migration Utility - Bulk Alias Update
 * Using this utility file, bulk update all specified content type nodes,
 * automatically setting their path alias, as well as creating any redirects
 * if necessary, during update.
 *
 * In the case that this script runs into memory limits, simply run it again
 * and it will pick back up where it last left off. This uses the Drupal State
 * API to continually count where the process is at.
 *
 * Commands
 * - Run Script: `drush scr web/modules/custom/surf_migrate/src/Utils/BulkURLAliasUpdate.php`
 * - Reset State: `drush sset surf_migrate.last_nid 0`
 *
 * Index
 * - 01 - Imports
 * - 02 - Content Types
 * - 03 - Query & Update
 */


/*------------------------------------*\
  01 - Imports
\*------------------------------------*/

use Drupal\pathauto\PathautoState;




/*------------------------------------*\
  02 - Content Types
\*------------------------------------*/

$content_types = [
  'article',
  'contract',
  'curriculum_module',
  'event',
  'experiment',
  'feature',
  'person',
  'press_release',
  'professional_development',
  'report_publication',
  'standard',
];




/*------------------------------------*\
  03 - Query & Update
\*------------------------------------*/

// Use Entity Type Manager to get Node-specific storage
$storage = Drupal::entityTypeManager()->getStorage('node');

// Constantly update this variable where the update process is
$last_nid = \Drupal::state()->get('surf_migrate.last_nid');

// Query all nodes relating to specified content types, in order, starting from
// last Node ID that was queried.
$nids = $storage->getQuery()
  ->condition('type', $content_types, 'IN')
  ->accessCheck(FALSE)
  ->condition('nid', $last_nid, '>')
  ->sort('nid')
  ->execute();

// Bulk update Path Aliases and print out progress.
foreach ($nids as $nid) {
  $node = $storage->load($nid);
  print_r('Saving node ' . $nid . PHP_EOL);
  $node->path->pathauto = PathautoState::CREATE;
  $node->save();
  \Drupal::service('pathauto.generator')
    ->updateEntityAlias($node, 'update');
  \Drupal::state()->set('nhmu_migrate.last_nid', $nid);
}
