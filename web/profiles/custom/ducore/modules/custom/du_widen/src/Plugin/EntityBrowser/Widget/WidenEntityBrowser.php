<?php

namespace Drupal\du_widen\Plugin\EntityBrowser\Widget;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\entity_browser\WidgetBase;
use Drupal\Core\Url;
use Drupal\entity_browser\WidgetValidationManager;
use Drupal\views\Entity\View as ViewEntity;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Uses a view to provide entity listing in a browser's widget.
 *
 * @EntityBrowserWidget(
 *   id = "widen",
 *   label = @Translation("Widen"),
 *   description = @Translation("Reference Widen Collective images."),
 *   provider = "views",
 *   auto_select = TRUE
 * )
 */
class WidenEntityBrowser extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * Widen max image width.
   *
   * @var int
   */
  const WIDEN_MAX_WIDTH = 4000;

  /**
   * Widen max image height.
   *
   * @var int
   */
  const WIDEN_MAX_HEIGHT = 4000;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('event_dispatcher'),
      $container->get('entity_type.manager'),
      $container->get('plugin.manager.entity_browser.widget_validation'),
      $container->get('current_user')
    );
  }

  /**
   * Constructs a new WidenEntityBrowser object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   Event dispatcher service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\entity_browser\WidgetValidationManager $validation_manager
   *   The Widget Validation Manager service.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EventDispatcherInterface $event_dispatcher, EntityTypeManagerInterface $entity_type_manager, WidgetValidationManager $validation_manager, AccountInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $event_dispatcher, $entity_type_manager, $validation_manager);
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public function getForm(array &$original_form, FormStateInterface $form_state, array $additional_widget_parameters) {
    $form = parent::getForm($original_form, $form_state, $additional_widget_parameters);
    // TODO - do we need better error handling for view and view_display (in
    // case either of those is nonexistent or display not of correct type)?
    $form['#attached']['library'] = ['entity_browser/view'];

    /** @var \Drupal\views\ViewExecutable $view */
    $view = $this->entityTypeManager
      ->getStorage('view')
      ->load('widen')
      ->getExecutable();

    // Check if the current user has access to this view.
    if (!$view->access('entity_browser')) {
      return [
        '#markup' => $this->t('You do not have access to this View.'),
      ];
    }

    if (!empty($this->configuration['arguments'])) {
      if (!empty($additional_widget_parameters['path_parts'])) {
        $arguments = [];
        // Map configuration arguments with original path parts.
        foreach ($this->configuration['arguments'] as $argument) {
          $arguments[] = isset($additional_widget_parameters['path_parts'][$argument]) ? $additional_widget_parameters['path_parts'][$argument] : '';
        }
        $view->setArguments(array_values($arguments));
      }
    }

    $form['view'] = $view->executeDisplay('entity_browser');

    if (empty($view->field['entity_browser_select'])) {
      $url = Url::fromRoute('entity.view.edit_form', ['view' => 'widen'])->toString();
      if ($this->currentUser->hasPermission('administer views')) {
        return [
          '#markup' => $this->t('Widen select form field not found on a view. <a href=":link">Go fix it</a>!', [':link' => $url]),
        ];
      }
      else {
        return [
          '#markup' => $this->t('Widen select form field not found on a view. Go fix it!'),
        ];
      }
    }

    // When rebuilding makes no sense to keep checkboxes that were previously
    // selected.
    $alt_text_settings = [];
    if (!empty($form['view']['entity_browser_select'])) {
      foreach (Element::children($form['view']['entity_browser_select']) as $child) {
        $form['view']['entity_browser_select'][$child]['#process'][] = ['\Drupal\du_widen\Plugin\EntityBrowser\Widget\WidenEntityBrowser', 'processCheckbox'];
        $form['view']['entity_browser_select'][$child]['#process'][] = ['\Drupal\Core\Render\Element\Checkbox', 'processAjaxForm'];
        $form['view']['entity_browser_select'][$child]['#process'][] = ['\Drupal\Core\Render\Element\Checkbox', 'processGroup'];
        list($id, $filename, $width, $height, $alt_text) = explode(':', $child);
        $alt_text_settings[$id] = $alt_text;
      }
    }

    $form['view']['view'] = [
      '#markup' => \Drupal::service('renderer')->render($form['view']['view']),
    ];

    // Let the user choose which additional image fields to add the selected
    // image to.
    $image_fields = \Drupal::request()->query->get('image_fields') ?? '';
    if (!empty($image_fields)) {
      $image_keys = explode('&', str_replace(['[', ']'], ['^', '~'], $image_fields));
      $image_fields = explode('&', $image_fields);
      $form['extras'] = ['#type' => 'fieldset'];
      $form['extras']['fields'] = [
        '#type' => 'checkboxes',
        '#title' => $this->t('Add Selection to Additional Image Fields'),
        '#options' => array_combine($image_keys, $image_fields),
        '#description' => $this->t('If none are selected, only the field originally selected will get the image added to it.'),
      ];
    }

    // Store the alt text for each element so we can fill in the alt text field
    // once one is selected.
    $form['#attached']['drupalSettings']['du_widen']['alt_text'] = $alt_text_settings;

    return $form;
  }

  /**
   * Sets the #checked property when rebuilding form.
   *
   * Every time when we rebuild we want all checkboxes to be unchecked.
   *
   * @see \Drupal\Core\Render\Element\Checkbox::processCheckbox()
   */
  public static function processCheckbox(&$element, FormStateInterface $form_state, &$complete_form) {
    if ($form_state->isRebuilding()) {
      $element['#checked'] = FALSE;
    }

    return $element;
  }

  /**
   * Get the size of the selected image(s).
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   * @param int $width
   *   The image's original width.
   * @param int $height
   *   The image's original height.
   * @param string $filename
   *   The image's filename.
   *
   * @return string
   *   Return the size in the format of 'HxWpx'.
   */
  private function getSize(FormStateInterface $form_state, $width = 0, $height = 0, $filename = '') {
    // Default to a square image size.
    $size = '800x800px';

    // If the file is an svg, set the size to a smaller size.
    $matches = [];
    preg_match('/.*\.svg$/', $filename, $matches);
    if (!empty($matches[0])) {
      return '200x200px';
    }

    // If width and height are passed in, use that as it's the image's original
    // size.
    if ($width && $height) {
      // Don't allow the size to exceed the maxes, otherwise the embed image URL
      // won't work.
      if ($width > self::WIDEN_MAX_WIDTH) {
        $percentage = self::WIDEN_MAX_WIDTH / $width;
        $width = self::WIDEN_MAX_WIDTH;
        $height = (int) ($height * $percentage);
      }
      if ($height > self::WIDEN_MAX_HEIGHT) {
        $percentage = self::WIDEN_MAX_HEIGHT / $height;
        $height = self::WIDEN_MAX_HEIGHT;
        $width = (int) ($width * $percentage);
      }
      $size = $width . 'x' . $height . 'px';
    }

    // Get size from the field max resolution.
    if ($form_state->has(['entity_browser', 'widget_context'])) {
      $context = $form_state->get(['entity_browser', 'widget_context']);
      if (!empty($context['upload_validators']['entity_browser_file_validate_image_resolution'][0])) {
        $size = $context['upload_validators']['entity_browser_file_validate_image_resolution'][0] . 'px';
      }
    }

    return $size;
  }

  /**
   * {@inheritdoc}
   */
  protected function prepareEntities(array $form, FormStateInterface $form_state) {
    $selected_rows = array_values(array_filter($form_state->getUserInput()['entity_browser_select']));
    $entities = [];
    foreach ($selected_rows as $row) {
      list($id, $filename, $width, $height, $alt_text) = explode(':', $row);
      $size = $this->getSize($form_state, $width, $height, $filename);
      $url = 'https://embed.widencdn.net/img/du/' . $id . '/' . $size . '/' . $filename;
      $file = $this->entityTypeManager->getStorage('file')->create([
        'uri' => $url,
        'uid' => $this->currentUser->id(),
      ]);

      $file->setPermanent();
      $entities[] = $file;
    }
    return $entities;
  }

  /**
   * {@inheritdoc}
   */
  public function validate(array &$form, FormStateInterface $form_state) {
    // If there weren't any errors set, run the normal validators.
    if (empty($form_state->getErrors())) {
      // Check if the file should allow for svg. Even if the field allows svg,
      // entity browser won't let us use svg because it's hardcoded to only
      // allow png, jpg, jpeg, and gif. Even the latest patch (#12) here doesn't
      // work because the image factory doesn't have svg as a supported type.
      // https://www.drupal.org/project/entity_browser/issues/2935400
      $context = $form_state->get(['entity_browser', 'widget_context']);
      if (!empty($context['target_file_mimetypes']) && in_array('image/svg+xml', $context['target_file_mimetypes'])) {
        $validators = $form_state->get(['entity_browser', 'validators']);
        $validators['file']['validators']['file_validate_extensions'][0] .= ' svg';
        $form_state->set(['entity_browser', 'validators'], $validators);
      }
      parent::validate($form, $form_state);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submit(array &$element, array &$form, FormStateInterface $form_state) {
    $entities = [];
    $entities = $this->prepareEntities($form, $form_state);
    $values = $form_state->getValues();
    foreach ($entities as $image) {
      if (!empty($values['fields'])) {
        $image_fields = array_filter($values['fields']) ?? '';
        $image->image_fields = array_map(function ($value) {
          return str_replace(['^', '~'], ['[', ']'], $value);
        }, array_keys($image_fields));
      }
      $image->save();
    }
    $this->selectEntities($entities, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    $dependencies = [];
    $view = ViewEntity::load('widen');
    $dependencies[$view->getConfigDependencyKey()] = [$view->getConfigDependencyName()];
    return $dependencies;
  }

  /**
   * {@inheritdoc}
   */
  public function access() {
    // Mark the widget as not visible if the user has no access to the view.
    /** @var \Drupal\views\ViewExecutable $view */
    $view = $this->entityTypeManager
      ->getStorage('view')
      ->load('widen')
      ->getExecutable();

    // Check if the current user has access to this view.
    return AccessResult::allowedIf($view->access('entity_browser'));
  }

}
