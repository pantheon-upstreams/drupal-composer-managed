<?php

namespace Drupal\du_widen\Plugin\EntityBrowser\SelectionDisplay;

use Drupal\Core\Form\FormStateInterface;
use Drupal\entity_browser\Plugin\EntityBrowser\SelectionDisplay\MultiStepDisplay;

/**
 * Show current selection and delivers selected entities.
 *
 * @EntityBrowserSelectionDisplay(
 *   id = "widen_multi_step_display",
 *   label = @Translation("Widen multi step selection display"),
 *   description = @Translation("Customization of the multi step selection display to handle widen images differently."),
 *   acceptPreselection = TRUE,
 *   js_commands = TRUE
 * )
 */
class WidenMultiStepDisplay extends MultiStepDisplay {

  /**
   * {@inheritdoc}
   */
  public function getForm(array &$original_form, FormStateInterface $form_state) {

    // Check if trigger element is dedicated to handle front-end commands.
    if (($triggering_element = $form_state->getTriggeringElement()) && $triggering_element['#name'] === 'ajax_commands_handler' && !empty($triggering_element['#value'])) {
      $this->executeJsCommand($form_state);
    }

    $selected_entities = $form_state->get(['entity_browser', 'selected_entities']);

    $form = [];
    $form['#attached']['library'][] = 'entity_browser/multi_step_display';
    $form['selected'] = [
      '#theme_wrappers' => ['container'],
      '#attributes' => ['class' => ['entities-list']],
      '#tree' => TRUE,
    ];
    if ($this->configuration['selection_hidden']) {
      $form['selected']['#attributes']['class'][] = 'hidden';
    }
    foreach ($selected_entities as $id => $entity) {
      $config = $this->configuration['display_settings'] + ['entity_type' => $this->configuration['entity_type']];

      // On Widen images, use the original_image image style so the user can see
      // what the image will look like.
      if (isset($entity->uri) && strpos($entity->uri->value, 'https://embed.widencdn.net') === 0) {
        $config['image_style'] = 'original_image';
      }

      $display_plugin = $this->fieldDisplayManager->createInstance(
        $this->configuration['display'],
        $config
      );
      $display = $display_plugin->view($entity);
      if (is_string($display)) {
        $display = ['#markup' => $display];
      }

      $form['selected']['items_' . $entity->id() . '_' . $id] = [
        '#theme_wrappers' => ['container'],
        '#attributes' => [
          'class' => ['item-container'],
          'data-entity-id' => $entity->id(),
        ],
        'display' => $display,
        'remove_button' => [
          '#type' => 'submit',
          '#value' => $this->t('Remove'),
          '#submit' => [[get_class($this), 'removeItemSubmit']],
          '#name' => 'remove_' . $entity->id() . '_' . $id,
          '#attributes' => [
            'class' => ['entity-browser-remove-selected-entity'],
            'data-row-id' => $id,
            'data-remove-entity' => 'items_' . $entity->id(),
          ],
        ],
        'weight' => [
          '#type' => 'hidden',
          '#default_value' => $id,
          '#attributes' => ['class' => ['weight']],
        ],
      ];
    }

    // Add hidden element used to make execution of front-end commands.
    $form['ajax_commands_handler'] = [
      '#type' => 'hidden',
      '#name' => 'ajax_commands_handler',
      '#id' => 'ajax_commands_handler',
      '#attributes' => ['id' => 'ajax_commands_handler'],
      '#ajax' => [
        'callback' => [get_class($this), 'handleAjaxCommand'],
        'wrapper' => 'edit-selected',
        'event' => 'execute_js_commands',
        'progress' => [
          'type' => 'fullscreen',
        ],
      ],
    ];

    $form['use_selected'] = [
      '#type' => 'submit',
      '#value' => $this->configuration['select_text'],
      '#name' => 'use_selected',
      '#attributes' => [
        'class' => ['entity-browser-use-selected', 'button--primary'],
      ],
      '#access' => empty($selected_entities) ? FALSE : TRUE,
    ];

    $form['show_selection'] = [
      '#type' => 'button',
      '#value' => $this->t('Show selected'),
      '#attributes' => [
        'class' => ['entity-browser-show-selection'],
      ],
      '#access' => empty($selected_entities) ? FALSE : TRUE,
    ];

    return $form;
  }

}
