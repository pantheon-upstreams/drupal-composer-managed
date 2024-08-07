<?php

namespace Drupal\imce;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\WidgetInterface;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\Url;

/**
 * Defines methods for integrating Imce into file field widgets.
 */
class ImceFileField implements TrustedCallbackInterface {

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks() {
    return ['preRenderWidget'];
  }

  /**
   * Returns a list of supported widgets.
   */
  public static function supportedWidgets() {
    $widgets = &drupal_static(__FUNCTION__);
    if (!isset($widgets)) {
      $widgets = ['file_generic', 'image_image'];
      \Drupal::moduleHandler()->alter('imce_supported_widgets', $widgets);
      $widgets = array_unique($widgets);
    }
    return $widgets;
  }

  /**
   * Checks if a widget is supported.
   */
  public static function isWidgetSupported(WidgetInterface $widget) {
    return in_array($widget->getPluginId(), static::supportedWidgets());
  }

  /**
   * Returns widget settings form.
   */
  public static function widgetSettingsForm(WidgetInterface $widget) {
    $form = [];
    if (static::isWidgetSupported($widget)) {
      $form['enabled'] = [
        '#type' => 'checkbox',
        '#title' => t('Allow users to select files from <a href=":url">Imce File Manager</a> for this field.', [':url' => Url::fromRoute('imce.admin')->toString()]),
        '#default_value' => $widget->getThirdPartySetting('imce', 'enabled'),
      ];
    }
    return $form;
  }

  /**
   * Alters the summary of widget settings form.
   */
  public static function alterWidgetSettingsSummary(&$summary, $context) {
    $widget = $context['widget'];
    if (static::isWidgetSupported($widget)) {
      $status = $widget->getThirdPartySetting('imce', 'enabled') ? t('Yes') : t('No');
      $summary[] = t('Imce enabled: @status', ['@status' => $status]);
    }
  }

  /**
   * Processes widget form.
   */
  public static function processWidget($element, FormStateInterface $form_state, $form) {
    // Path input.
    $element['imce_paths'] = [
      '#type' => 'hidden',
      '#attributes' => [
        'class' => ['imce-filefield-paths'],
        'data-imce-url' => Url::fromRoute('imce.page', ['scheme' => $element['#scheme']])->toString(),
      ],
      // Reset value to prevent consistent errors.
      '#value' => '',
    ];
    // Library.
    $element['#attached']['library'][] = 'imce/drupal.imce.filefield';
    // Set the pre-renderer to conditionally disable the elements.
    $element['#pre_render'][] = [get_called_class(), 'preRenderWidget'];
    return $element;
  }

  /**
   * Pre-renders widget form.
   */
  public static function preRenderWidget($element) {
    // Hide elements if there is already an uploaded file.
    if (!empty($element['#value']['fids'])) {
      $element['imce_paths']['#access'] = FALSE;
    }
    return $element;
  }

  /**
   * Sets widget file id values by validating and processing the submitted data.
   *
   * Runs before processor callbacks.
   */
  public static function setWidgetValue($element, &$input, FormStateInterface $form_state) {
    if (empty($input['imce_paths'])) {
      return;
    }
    $paths = $input['imce_paths'];
    $input['imce_paths'] = '';
    // Remove excess data.
    $paths = array_unique(array_filter(explode(':', $paths)));
    if (isset($element['#cardinality']) && $element['#cardinality'] > -1) {
      $paths = array_slice($paths, 0, $element['#cardinality']);
    }
    // Check if paths are accessible by the current user with Imce.
    if (!$paths = Imce::accessFilePaths($paths, \Drupal::currentUser(), $element['#scheme'])) {
      return;
    }
    // Validate paths as file entities.
    $file_usage = \Drupal::service('file.usage');
    $errors = [];
    foreach ($paths as $path) {
      // Get entity by uri.
      $file = Imce::getFileEntity($element['#scheme'] . '://' . $path, TRUE);
      if ($new_errors = file_validate($file, $element['#upload_validators'])) {
        $errors = array_merge($errors, $new_errors);
      }
      else {
        // Save the file record.
        if ($file->isNew()) {
          $file->save();
        }
        if ($fid = $file->id()) {
          // Make sure the file has usage otherwise it will be denied.
          if (!$file_usage->listUsage($file)) {
            $file_usage->add($file, 'imce', 'file', $fid);
          }
          $input['fids'][] = $fid;
        }
      }
    }
    // Set error messages.
    if ($errors) {
      $errors = array_unique($errors);
      if (count($errors) > 1) {
        $errors = ['#theme' => 'item_list', '#items' => $errors];
        $message = \Drupal::service('renderer')->render($errors);
      }
      else {
        $message = array_pop($errors);
      }
      // May break the widget flow if set as a form error.
      \Drupal::messenger()
        ->addMessage($message, 'error');
    }
  }

}
