<?php

namespace Drupal\du_site\Form;

use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\BaseFormIdInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\filter\Entity\FilterFormat;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\editor\Ajax\EditorDialogSave;
use Drupal\Core\Ajax\CloseModalDialogCommand;

/**
 * Provides a link dialog for text editors.
 */
class DUSiteCKEditorEntityLinkDialog extends FormBase implements BaseFormIdInterface {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ckeditor_entity_link_dialog';
  }

  /**
   * {@inheritdoc}
   */
  public function getBaseFormId() {
    // Use the EditorLinkDialog form id to ease alteration.
    return 'editor_link_dialog';
  }

  /**
   * {@inheritdoc}
   *
   * @param array $form
   *   Form element.
   * @param Drupal\Core\Form\FormStateInterface $form_state
   *   Form State object.
   * @param \Drupal\filter\Entity\FilterFormat $filter_format
   *   The filter format for which this dialog corresponds.
   *
   * @return mixed
   *   Returns form object.
   */
  public function buildForm(array $form, FormStateInterface $form_state, FilterFormat $filter_format = NULL) {
    $config = $this->config('ckeditor_entity_link.settings');

    // The default values are set directly from \Drupal::request()->request,
    // provided by the editor plugin opening the dialog.
    $user_input = $form_state->getUserInput();
    $input = isset($user_input['editor_object']) ? $user_input['editor_object'] : [];

    $form['#tree'] = TRUE;
    $form['#attached']['library'][] = 'editor/drupal.editor.dialog';
    $form['#prefix'] = '<div id="ckeditor-entity-link-dialog-form">';
    $form['#suffix'] = '</div>';

    $entity_types = \Drupal::entityTypeManager()->getDefinitions();
    $types = [];
    foreach ($config->get('entity_types') as $type => $selected) {
      if ($selected) {
        $types[$type] = $entity_types[$type]->getLabel();
      }
    }
    $types['url'] = $this->t('URL');

    $entity_type = empty($form_state->getValue('entity_type')) ? 'node' : $form_state->getValue('entity_type');
    if (!empty($input['href'])) {
      $entity_type = 'url';
    }

    if (empty($entity_type)) {
      $entity_type = 'node';
    }

    $form['entity_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Link type'),
      '#options' => $types,
      '#default_value' => $entity_type,
      '#required' => FALSE,
      '#size' => 1,
      '#ajax' => [
        'callback' => '::updateTypeSettings',
        'effect' => 'fade',
      ],
    ];

    $bundles = [];
    foreach ($config->get($entity_type . '_bundles') as $bundle => $selected) {
      if ($selected) {
        $bundles[] = $bundle;
      }
    }

    if ($entity_type == 'url') {
      $form['entity_id'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Link'),
        '#default_value' => $input['href'],
        '#maxlength' => 2048,
        '#required' => FALSE,
        '#prefix' => '<div id="entity-id-wrapper">',
        '#suffix' => '</div>',
      ];
    }
    else {
      $form['entity_id'] = [
        '#type' => 'entity_autocomplete',
        '#target_type' => $entity_type,
        '#title' => $this->t('Link'),
        '#maxlength' => 2048,
        '#required' => TRUE,
        '#prefix' => '<div id="entity-id-wrapper">',
        '#suffix' => '</div>',
      ];
    }

    if (!empty($bundles)) {
      $form['entity_id']['#selection_settings']['target_bundles'] = $bundles;
    }

    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['save_modal'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      // No regular submit-handler. This form only works via JavaScript.
      '#submit' => [],
      '#ajax' => [
        'callback' => '::submitForm',
        'event' => 'click',
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   *
   * @param array $form
   *   Form element.
   * @param Drupal\Core\Form\FormStateInterface $form_state
   *   Form State object.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    if ($form_state->getErrors()) {
      unset($form['#prefix'], $form['#suffix']);
      $form['status_messages'] = [
        '#type' => 'status_messages',
        '#weight' => -10,
      ];
      $response->addCommand(new HtmlCommand('#ckeditor-entity-link-dialog-form', $form));
    }
    else {
      $form_values = $form_state->getValues();
      $type = $form_values['entity_type'];

      if ($type == 'url') {
        $entity = $form_values['entity_id'];
        $values = [
          'attributes' => [
            'href' => $entity,
          ] + $form_state->getValue('attributes', []),
        ];
      }
      else {
        $entity = \Drupal::service('entity_type.manager')
          ->getStorage($form_state->getValue('entity_type'))
          ->load($form_state->getValue('entity_id'));

        $values = [
          'attributes' => [
            'href' => $this->getUrl($entity),
          ] + $form_state->getValue('attributes', []),
        ];
      }

      $response->addCommand(new EditorDialogSave($values));
      $response->addCommand(new CloseModalDialogCommand());
    }

    return $response;
  }

  /**
   * Helper function to return entity url.
   *
   * @param Drupal\Core\Entity\EntityInterface $entity
   *   Entity object.
   *
   * @return string
   *   Entity url.
   */
  public function getUrl(EntityInterface $entity) {
    switch ($entity->getEntityType()->get('id')) {
      case 'menu_link_content':
        return $entity->getUrlObject()->toString();

      case 'shortcut':
        return $entity->getUrl()->toString();

      case 'file':
        return \Drupal::service('file_url_generator')->transformRelative(\Drupal::service('file_url_generator')->generateAbsoluteString($entity->uri->value));

      default:
        return $entity->toUrl()->toString();
    }
  }

  /**
   * Ajax callback to update the form fields which depend on embed type.
   *
   * @param array $form
   *   The build form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return Drupal\Core\Ajax|AjaxResponse
   *   Ajax response with updated options for the embed type.
   */
  public function updateTypeSettings(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    // Update options for entity type bundles.
    $response->addCommand(new ReplaceCommand(
      '#entity-id-wrapper',
      $form['entity_id']
    ));

    return $response;
  }

}
