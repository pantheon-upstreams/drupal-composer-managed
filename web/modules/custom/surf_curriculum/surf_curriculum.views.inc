<?php

/**
 * Implements hook_views_data_alter().
 */
function surf_curriculum_views_data_alter(array &$data) {
  $data['node']['webform_link']['field'] = [
    'id' => 'surf_curriculum_webform_link',
    'title' => t('Link to webform'),
    'help'=> t('Link to the webform with the node reference prepopulated'),
  ];
}