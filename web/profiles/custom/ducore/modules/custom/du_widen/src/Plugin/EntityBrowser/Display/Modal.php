<?php

namespace Drupal\du_widen\Plugin\EntityBrowser\Display;

use Drupal\entity_browser\DisplayBase;
use Drupal\entity_browser\Events\Events;
use Drupal\entity_browser\Events\RegisterJSCallbacks;
use Drupal\entity_browser\Events\AlterEntityBrowserDisplayData;
use Drupal\entity_browser\Plugin\EntityBrowser\Display\Modal as EntityBrowserModal;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Ajax\OpenDialogCommand;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Url;

/**
 * Presents entity browser in a Modal.
 *
 * @EntityBrowserDisplay(
 *   id = "du_modal",
 *   label = @Translation("DU Modal"),
 *   description = @Translation("Same as the regular Modal with some modifications for DU specific functionality."),
 *   uses_route = TRUE
 * )
 */
class Modal extends EntityBrowserModal {

  /**
   * {@inheritdoc}
   *
   * Changes from the EntityBrowser Modal:
   * - Register our custom selection completed callback.
   * - Find image fields from source form and pass through the query.
   */
  public function displayEntityBrowser(array $element, FormStateInterface $form_state, array &$complete_form, array $persistent_data = []) {
    DisplayBase::displayEntityBrowser($element, $form_state, $complete_form, $persistent_data);
    $js_event_object = new RegisterJSCallbacks($this->configuration['entity_browser_id'], $this->getUuid());
    $js_event_object->registerCallback('Drupal.du_widen.selectionCompleted');

    // Create an event object for dispatching.
    $js_event = $this->eventDispatcher->dispatch($js_event_object, Events::REGISTER_JS_CALLBACKS);

    $original_path = $this->currentPath->getPath();
    $entity_browser_id = $this->configuration['entity_browser_id'];

    // Find entity browsers that use the widen widget, since the only use of
    // finding image fields is within the widen widget.
    $included_browsers = [];
    $entity_browsers = \Drupal::entityTypeManager()->getStorage('entity_browser')->loadMultiple();
    foreach ($entity_browsers as $browser) {
      foreach ($browser->getWidgets() as $widget) {
        if ($widget->id() == 'widen') {
          $included_browsers[] = $browser->getName();
        }
      }
    }

    // Find all image fields to pass through the query so that the Entity
    // Browser Widget has access to them.
    $image_fields = '';
    if (!empty($included_browsers)) {
      $image_fields = $this->findImageFields($complete_form, $included_browsers);
      $image_fields = implode('&', $image_fields);
    }

    $data = [
      'query_parameters' => [
        'query' => [
          'uuid' => $this->getUuid(),
          'original_path' => $original_path,
          'image_fields' => $image_fields,
          'resolution_limit' => !empty($persistent_data['validators']['file']['validators']['entity_browser_file_validate_image_resolution']),
        ],
      ],
      'attributes' => [
        'data-uuid' => $this->getUuid(),
      ],
    ];
    $event_object = new AlterEntityBrowserDisplayData($entity_browser_id, $this->getUuid(), $this->getPluginDefinition(), $form_state, $data);
    $event = $this->eventDispatcher->dispatch($event_object, Events::ALTER_BROWSER_DISPLAY_DATA);
    $data = $event->getData();
    return [
      '#theme_wrappers' => ['container'],
      'path' => [
        '#type' => 'hidden',
        '#value' => Url::fromRoute('entity_browser.' . $entity_browser_id, [], $data['query_parameters'])->toString(),
      ],
      'open_modal' => [
        '#type' => 'submit',
        '#value' => $this->configuration['link_text'],
        '#limit_validation_errors' => [],
        '#submit' => [],
        '#name' => implode('_', $element['#eb_parents']),
        '#ajax' => [
          'callback' => [$this, 'openModal'],
          'event' => 'click',
        ],
        '#executes_submit_callback' => FALSE,
        '#attributes' => $data['attributes'],
        '#attached' => [
          'library' => [
            'core/drupal.dialog.ajax',
            'entity_browser/modal',
            'du_widen/common',
          ],
          'drupalSettings' => [
            'entity_browser' => [
              $this->getUuid() => [
                'auto_open' => $this->configuration['auto_open'],
              ],
              'modal' => [
                $this->getUuid() => [
                  'uuid' => $this->getUuid(),
                  'js_callbacks' => $js_event->getCallbacks(),
                  'original_path' => $original_path,
                  'auto_open' => $this->configuration['auto_open'],
                ],
              ],
            ],
          ],
        ],
      ],
    ];
  }

  /**
   * Generates the content and opens the modal.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   An ajax response.
   */
  public function openModal(array &$form, FormStateInterface $form_state) {
    $triggering_element = $form_state->getTriggeringElement();
    $parents = $triggering_element['#parents'];
    array_pop($parents);
    $parents = array_merge($parents, ['path']);
    $input = $form_state->getUserInput();
    $src = NestedArray::getValue($input, $parents);

    $field_name = $triggering_element['#parents'][0];
    $element_name = $this->configuration['entity_browser_id'];
    $name = 'entity_browser_iframe_' . $element_name;
    $content = [
      '#prefix' => '<div class="ajax-progress-throbber"></div>',
      '#type' => 'html_tag',
      '#tag' => 'iframe',
      '#attributes' => [
        'src' => $src,
        'class' => 'entity-browser-du-modal-iframe',
        'width' => '100%',
        'frameborder' => 0,
        'style' => 'padding:0; position:relative; z-index:10002;',
        'name' => $name,
        'id' => $name,
      ],
    ];
    if (!empty($this->configuration['height']) && is_numeric($this->configuration['height']) && $this->configuration['height'] > 90) {
      $content['#attributes']['height'] = $this->configuration['height'] - 90;
    }
    $html = $this->renderer->render($content);

    $response = new AjaxResponse();
    $response->addCommand(new OpenDialogCommand('#' . Html::getUniqueId($field_name . '-' . $element_name . '-dialog'), $this->configuration['link_text'], $html, [
      'width' => 'auto',
      'height' => 'auto',
      'modal' => TRUE,
      'maxWidth' => $this->configuration['width'],
      'maxHeight' => $this->configuration['height'],
      'fluid' => 1,
      'autoResize' => 0,
      'resizable' => 0,
      'classes' => ['ui-dialog' => 'entity-browser-du-modal'],
    ]));
    return $response;
  }

  /**
   * KernelEvents::RESPONSE listener.
   *
   * Intercepts default response and injects response that will trigger JS to
   * propagate selected entities upstream.
   *
   * @param \Symfony\Component\HttpKernel\Event\ResponseEvent $event
   *   Response event.
   */
  public function propagateSelection(ResponseEvent $event) {
    $render = [
      '#attached' => [
        'library' => ['du_widen/modal_selection'],
        'drupalSettings' => [
          'entity_browser' => [
            'modal' => [
              'entities' => array_map(function (EntityInterface $item) {
                return [
                  $item->id(),
                  $item->uuid(),
                  $item->getEntityTypeId(),
                  $item->image_fields ?? '',
                ];
              }, $this->entities),
              'uuid' => $this->request->query->get('uuid'),
            ],
          ],
        ],
      ],
    ];

    $event->setResponse($this->bareHtmlPageRenderer->renderBarePage($render, $this->t('Entity browser'), 'page'));
  }

  /**
   * Find image fields within the form.
   *
   * @param array $form
   *   The form array.
   * @param array $entity_browsers
   *   Array of entity browsers that the field must use in order to be included.
   *
   * @return array
   *   Return an array of image fields that match.
   */
  protected function findImageFields(array $form, array $entity_browsers = []) {
    $image_fields = [];
    $children = Element::children($form);
    foreach ($children as $field) {
      if (!empty($form[$field]['widget']['entity_browser'])) {
        $widget = $form[$field]['widget'];
        $entity_browser = $form[$field]['widget']['entity_browser'];
        if (
          in_array($entity_browser['#entity_browser'], $entity_browsers)
          && in_array('image/jpeg', $entity_browser['#widget_context']['target_file_mimetypes'])
        ) {
          if (empty($entity_browser['#name'])) {
            $name = '';
            if (!empty($widget['#field_parents'])) {
              $name = $widget['#field_parents'][0] . '[' . $widget['#field_parents'][1] . '][' . $widget['#field_parents'][2] . '][' . $widget['#field_name'] . ']';
              $image_fields[] = $name;
            }
            elseif (!empty($widget['#field_name'])) {
              $image_fields[] = $widget['#field_name'];

            }
          }
          else {
            $image_fields[] = str_replace('[entity_browser]', '', $entity_browser['#name']);
          }
        }
      }

      if (!empty($form[$field]['widget'][0]['subform'])) {
        $subform = $form[$field]['widget'][0]['subform'];
        $image_fields = array_merge($image_fields, $this->findImageFields($subform, $entity_browsers));
      }
    }

    return array_filter($image_fields);
  }

}
