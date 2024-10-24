<?php

/**
 * @file
 * Widen Views backend.
 */

/**
 * Implements hook_views_data().
 */
function du_widen_views_data() {
  $data = [];

  $data['widen']['table']['group'] = t('Widen');
  $data['widen']['table']['base'] = [
    'title' => t('Widen Collective Assets'),
    'help' => t('Query for Widen assets using the Widen Collective API.'),
    'query_id' => 'widen_query',
  ];

  if (\Drupal::moduleHandler()->moduleExists('entity_browser')) {
    $data['widen']['entity_browser_select'] = [
      'title' => t('Widen browser bulk select form'),
      'help' => t('Add a form element that lets you use a view as a base to select entities in entity browser.'),
      'field' => [
        'id' => 'widen_select',
      ],
    ];
  }

  $data['widen']['id'] = [
    'title'  => t('ID'),
    'help'   => t('The asset ID.'),
    'field'  => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'argument' => [
      'id' => 'string',
    ],
    'field_alias' => 'id',
  ];

  $data['widen']['query'] = [
    'title'  => t('q'),
    'help'   => t('The query string to search for. Using the search filter will ignore other filters.'),
    'filter' => [
      'id' => 'widen_filter_text',
    ],
    'argument' => [
      'id' => 'string',
    ],
  ];

  $data['widen']['external_id'] = [
    'title'  => t('External ID'),
    'help'   => t('The external ID.'),
    'field'  => [
      'id' => 'standard',
    ],
  ];

  $data['widen']['filename'] = [
    'title'  => t('Filename'),
    'help'   => t('The filename.'),
    'field'  => [
      'id' => 'standard',
    ],
  ];

  $data['widen']['thumbnail'] = [
    'title'  => t('Thumbnail'),
    'help'   => t('The thumbnail image.'),
    'field'  => [
      'id' => 'standard',
    ],
  ];

  $data['widen']['width'] = [
    'title'  => t('Width'),
    'help'   => t('The image width.'),
    'field'  => [
      'id' => 'standard',
    ],
  ];

  $data['widen']['height'] = [
    'title'  => t('Height'),
    'help'   => t('The image height.'),
    'field'  => [
      'id' => 'standard',
    ],
  ];

  $data['widen']['alt_text'] = [
    'title'  => t('Alt Text'),
    'help'   => t('The alt text for the image.'),
    'field'  => [
      'id' => 'standard',
    ],
  ];

  return $data;
}
