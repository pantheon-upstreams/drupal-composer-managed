<?php

declare(strict_types = 1);

namespace Drupal\Tests\imce\Kernel;

use Drupal\editor\Entity\Editor;
use Drupal\filter\Entity\FilterFormat;
use Drupal\Tests\ckeditor5\Kernel\SmartDefaultSettingsTest;

/**
 * @covers \Drupal\imce\Plugin\CKEditor4To5Upgrade\Imce
 * @group imce
 * @group ckeditor5
 * @requires module ckeditor5
 * @internal
 */
class CKE4to5UpgradePathTest extends SmartDefaultSettingsTest {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'imce',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    FilterFormat::create([
      'format' => 'both',
      'name' => 'Both IMCE buttons',
      'filters' => [
        'filter_html' => [
          'status' => 1,
          'settings' => [
            'allowed_html' => '<p> <br> <strong> <h2> <h3> <img src alt height width data-entity-type data-entity-uuid> <a href>',
          ],
        ],
      ],
    ])->setSyncing(TRUE)->save();
    FilterFormat::create([
      'format' => 'image_only',
      'name' => 'Only the IMCE image button',
      'filters' => [
        'filter_html' => [
          'status' => 1,
          'settings' => [
            'allowed_html' => '<p> <br> <strong> <h2> <h3> <img src alt height width data-entity-type data-entity-uuid>',
          ],
        ],
      ],
    ])->setSyncing(TRUE)->save();
    FilterFormat::create([
      'format' => 'link_only',
      'name' => 'Only the IMCE link button',
      'filters' => [
        'filter_html' => [
          'status' => 1,
          'settings' => [
            'allowed_html' => '<p> <br> <strong> <h2> <h3> <a href>',
          ],
        ],
      ],
    ])->setSyncing(TRUE)->save();

    $generate_editor_settings = function (array $imce_buttons) {
      return [
        'toolbar' => [
          'rows' => [
            0 => [
              [
                'name' => 'Basic Formatting',
                'items' => [
                  'Bold',
                  'Format',
                ],
              ],
              [
                'name' => 'IMCE buttons',
                'items' => $imce_buttons,
              ],
            ],
          ],
        ],
        'plugins' => [
          // The CKEditor 4 plugin functionality has no settings.
        ],
      ];
    };

    Editor::create([
      'format' => 'both',
      'editor' => 'ckeditor',
      'settings' => $generate_editor_settings([
        'ImceImage',
        'ImceLink',
      ]),
    ])->setSyncing(TRUE)->save();
    Editor::create([
      'format' => 'image_only',
      'editor' => 'ckeditor',
      'settings' => $generate_editor_settings([
        'ImceImage',
      ]),
    ])->setSyncing(TRUE)->save();
    Editor::create([
      'format' => 'link_only',
      'editor' => 'ckeditor',
      'settings' => $generate_editor_settings([
        'ImceLink',
      ]),
    ])->setSyncing(TRUE)->save();
  }

  /**
   * {@inheritdoc}
   */
  public function provider() {
    parent::provider();

    $expected_ckeditor5_plugin_settings = [
      'plugins' => [
        'ckeditor5_heading' => [
          'enabled_headings' => [
            'heading2',
            'heading3',
          ],
        ],
      ],
    ];

    yield "both IMCE buttons" => [
      'format_id' => 'both',
      'filters_to_drop' => [],
      'expected_ckeditor5_settings' => [
        'toolbar' => [
          'items' => [
            'bold',
            'heading',
            '|',
            'imce_image',
            'imce_link',
          ],
        ],
      ] + $expected_ckeditor5_plugin_settings,
      'expected_superset' => '',
      'expected_fundamental_compatibility_violations' => [],
      'expected_db_logs' => [],
      'expected_messages' => [],
    ];
    yield "IMCE image button only" => [
      'format_id' => 'image_only',
      'filters_to_drop' => [],
      'expected_ckeditor5_settings' => [
        'toolbar' => [
          'items' => [
            'bold',
            'heading',
            '|',
            'imce_image',
          ],
        ],
      ] + $expected_ckeditor5_plugin_settings,
      'expected_superset' => '',
      'expected_fundamental_compatibility_violations' => [],
      'expected_db_logs' => [],
      'expected_messages' => [],
    ];
    yield "IMCE link button only" => [
      'format_id' => 'link_only',
      'filters_to_drop' => [],
      'expected_ckeditor5_settings' => [
        'toolbar' => [
          'items' => [
            'bold',
            'heading',
            '|',
            'imce_link',
          ],
        ],
      ] + $expected_ckeditor5_plugin_settings,
      'expected_superset' => '',
      'expected_fundamental_compatibility_violations' => [],
      'expected_db_logs' => [],
      'expected_messages' => [],
    ];
  }

}
