<?php

namespace Drupal\section_library;

use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\Url;
use Drupal\Component\Serialization\Json;

/**
 * Add Pre-render trusted callback for Section library.
 */
class SectionLibraryRender implements TrustedCallbackInterface {

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks() {
    return ['preRender'];
  }

  /**
   * Pre-render callback for layout builder.
   */
  public static function preRender($elements) {
    if (isset($elements['layout_builder'])) {
      $sections = $elements['layout_builder'];
      foreach ($sections as $key => $section) {
        // Filter the sections.
        if (!is_numeric($key)) {
          continue;
        }

        // Add import from library link to the new section.
        if (isset($section['link']) && isset($section['link']['#url'])) {
          $params = $section['link']['#url']->getRouteParameters();
          $url_options = [
            'attributes' => [
              'class' => [
                'use-ajax',
                'layout-builder__link',
                'layout-builder__link--import-from-library',
              ],
              'data-dialog-type' => 'dialog',
              'data-dialog-renderer' => 'off_canvas',
            ],
          ];

          // Optional showing the library on Layout Builder Modal.
          $moduleHandler = \Drupal::service('module_handler');
          if ($moduleHandler->moduleExists('layout_builder_modal')) {
            $config = \Drupal::config('layout_builder_modal.settings');

            $data_dialog_options = Json::encode([
              'width' => $config->get('modal_width'),
              'height' => $config->get('modal_height'),
              'target' => 'layout-builder-modal',
              'autoResize' => $config->get('modal_autoresize'),
              'modal' => TRUE,
            ]);

            $url_options['attributes']['data-dialog-options'] = $data_dialog_options;
            unset($url_options['attributes']['data-dialog-renderer']);
          }

          $choose_template_from_library_url = Url::fromRoute(
            'section_library.choose_template_from_library',
            [
              'section_storage_type' => $params['section_storage_type'],
              'section_storage' => $params['section_storage'],
              'delta' => $params['delta'],
            ],
            $url_options
          );
          $sections[$key]['choose_template_from_library'] = [
            '#type' => 'link',
            '#title' => t('Import from Library'),
            '#url' => $choose_template_from_library_url,
            '#access' => $choose_template_from_library_url->access(),
          ];
        }
        // Add save to library link to the built section.
        elseif (isset($section['configure']) && isset($section['configure']['#url'])) {
          $params = $section['configure']['#url']->getRouteParameters();
          // Pop the last item to add the link before it.
          $last_item_section = array_pop($sections[$key]);
          $add_to_library_url = Url::fromRoute(
            'section_library.add_section_to_library',
            [
              'section_storage_type' => $params['section_storage_type'],
              'section_storage' => $params['section_storage'],
              'delta' => $params['delta'],
            ],
            [
              'attributes' => [
                'class' => [
                  'use-ajax',
                  'layout-builder__link',
                  'layout-builder__link--add-section-to-library',
                ],
                'data-dialog-type' => 'dialog',
                'data-dialog-renderer' => 'off_canvas',
              ],
            ]
          );
          $sections[$key]['add_to_library'] = [
            '#type' => 'link',
            '#title' => t('Add to Library'),
            '#url' => $add_to_library_url,
            '#access' => $add_to_library_url->access(),
          ];
          // Push the last item again.
          array_push($sections[$key], $last_item_section);
        }
      }

      $add_template_to_library_url = Url::fromRoute(
        'section_library.add_template_to_library',
        [
          'section_storage_type' => $params['section_storage_type'],
          'section_storage' => $params['section_storage'],
          'delta' => $params['delta'],
        ],
        [
          'attributes' => [
            'class' => [
              'use-ajax',
              'layout-builder__link',
              'layout-builder__link--add-template-to-library',
            ],
            'data-dialog-type' => 'dialog',
            'data-dialog-renderer' => 'off_canvas',
          ],
        ]
      );
      $add_to_template = [
        'add_template_to_library' => [
          '#type' => 'link',
          '#title' => t('Add this template to library'),
          '#url' => $add_template_to_library_url,
          '#access' => $add_template_to_library_url->access(),
        ],
      ];
      array_unshift($sections, $add_to_template);

      $elements['layout_builder'] = $sections;

      // Attach the library.
      $elements['#attached']['library'][] = 'section_library/section_library';
    }
    return $elements;
  }

}
