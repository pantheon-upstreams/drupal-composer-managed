<?php

namespace Drupal\media_library_extend\Form;

use Drupal\Core\Ajax\CloseDialogCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\SubformState;
use Drupal\Core\Pager\PagerManagerInterface;
use Drupal\Core\Pager\PagerParametersInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Url;
use Drupal\media_library\MediaLibraryState;
use Drupal\media_library\OpenerResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class PaneSelectForm.
 *
 * @package Drupal\media_library_extend\Form
 */
class PaneSelectForm extends FormBase {

  /**
   * The currently active request object.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * The media library opener resolver service.
   *
   * @var \Drupal\media_library\OpenerResolverInterface
   */
  protected $openerResolver;

  /**
   * The media library pane the form is being built for.
   *
   * @var \Drupal\media_library_extend\Entity\MediaLibraryPaneInterface
   */
  protected $pane;

  /**
   * The source plugin corresponding to the active media library pane.
   *
   * @var \Drupal\media_library_extend\Plugin\MediaLibrarySourceInterface
   */
  protected $plugin;

  /**
   * The current page being rendered.
   *
   * @var int
   */
  protected $page;

  /**
   * The total result cound provided by the source plugin.
   *
   * @var int
   */
  protected $resultCount;

  /**
   * The pager manager.
   *
   * @var \Drupal\Core\Pager\PagerManagerInterface
   */
  protected $pagerManager;

  /**
   * The pager parameters.
   *
   * @var \Drupal\Core\Pager\PagerParametersInterface
   */
  protected $pagerParameters;

  /**
   * {@inheritdoc}
   */
  public function __construct(RequestStack $request_stack, OpenerResolverInterface $opener_resolver, PagerManagerInterface $pager_manager, PagerParametersInterface $pager_parameters) {
    $this->request = $request_stack->getCurrentRequest();
    $this->openerResolver = $opener_resolver;
    $this->pagerManager = $pager_manager;
    $this->pagerParameters = $pager_parameters;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack'),
      $container->get('media_library.opener_resolver'),
      $container->get('pager.manager'),
      $container->get('pager.parameters')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'media_library_extend_pane_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // @todo Remove in https://www.drupal.org/project/drupal/issues/2504115
    // Currently the default URL for all AJAX form elements is the current URL,
    // not the form action. This causes bugs when this form is rendered from an
    // AJAX path like /views/ajax, which cannot process AJAX form submits.
    $query = $this->request->query->all();
    $query[FormBuilderInterface::AJAX_FORM_REQUEST] = TRUE;

    // @todo Cleanly read target bundle from MediaLibraryState.
    $this->target_bundle = $query['media_library_selected_type'];
    $this->pane = $form_state->get('pane');
    $this->plugin = $form_state->get('source_plugin');

    $this->addFilters($form, $form_state, $query);
    $this->addPreviews($form, $form_state, $query);
    $this->addPager($form, $form_state, $query);

    $form['#theme'] = 'media_library_pane';
    $form['#attributes']['class'][] = 'media-library-view';
    $form['#attributes']['data-element-id'] = 'media_library_select_form';
    $form['#attached']['library'][] = 'media_library_extend/ui';

    // Add 'Insert selected' button.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#ajax' => [
        'url' => Url::fromRoute('media_library.ui'),
        'options' => [
          'query' => $query,
        ],
        'callback' => '::ajaxSubmitForm',
      ],
      '#value' => $this->t('Insert selected'),
      '#button_type' => 'primary',
      '#field_id' => 'form_selection',
      '#attributes' => [
        'class' => [
          'media-library-select',
        ],
        // By default, the AJAX system tries to move the focus back to the
        // element that triggered the AJAX request. Since the media library is
        // closed after clicking the select button, the focus can't be moved
        // back. We need to set the 'data-disable-refocus' attribute to prevent
        // the AJAX system from moving focus to a random element. The select
        // button triggers an update in the opener, and the opener should be
        // responsible for moving the focus. An example of this can be seen in
        // MediaLibraryWidget::updateWidget().
        // @see \Drupal\media_library\Plugin\Field\FieldWidget\MediaLibraryWidget::updateWidget()
        'data-disable-refocus' => 'true',
      ],
    ];

    return $form;
  }

  /**
   * Adds plugin filters to the pane's form.
   *
   * @param array $form
   *   The current form to modify.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current form state.
   * @param array $query
   *   Current query parameters for link generation.
   */
  protected function addFilters(array &$form, FormStateInterface $form_state, array $query) {
    // Offer filtering and search.
    $form['#parents'] = [];
    $form['filters'] = [
      '#parents' => ['filters'],
      '#type' => 'fieldset',
      '#title' => $this->t('Filters'),
      '#tree' => TRUE,
    ];

    $form['filters']['actions'] = [
      // @todo Should by type 'actions'.
      '#type' => 'container',
      '#weight' => 100,
    ];
    $form['filters']['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Apply'),
      '#ajax' => [
        'url' => Url::fromRoute('media_library.ui'),
        'options' => [
          'query' => [
            'page' => 0,
            FormBuilderInterface::AJAX_FORM_REQUEST => TRUE,
          ] + $query,
        ],
        'callback' => '::ajaxFilterForm',
        'wrapper' => 'media-library-extend-form-content',
      ],
    ];

    $subform_state = SubformState::createForSubform($form['filters'], $form, $form_state);
    $form['filters'] = $this->plugin->buildform($form['filters'], $subform_state);

    $elements = Element::children($form['filters']);
    if ($elements === ['actions']) {
      // Actions element is likely present, but useless without filters.
      // Do not show empty filter container.
      // We do not deny access, because Javascript needs to be able to use
      // the filter button as an input element.
      $form['filters']['#attributes']['class'][] = 'visually-hidden';
    }

    // Set initial values from form state.
    $this->setPluginState($form, $form_state);

    $items_per_page = $this->getItemsPerPage();
    // Add current page after determining result count.
    $this->resultCount = $this->plugin->getCount();
    if (!is_null($this->resultCount)) {
      $pager = $this->pagerManager->createPager($this->resultCount, $items_per_page);
      $this->page = $pager->getCurrentPage();
    }
    else {
      // @todo Give countless plugins a way to say that there are no more
      // results left.
      // Get current page.
      $requested_page = $this->pagerParameters->findPage();
      // Set number of items high enough to allow another page to be shown.
      $pager = $this->pagerManager->createPager(($requested_page + 1) * $items_per_page + 1, $items_per_page);
      $this->page = $pager->getCurrentPage();
    }

    // Update plugin state with pager info.
    $this->plugin->setValue('page', $this->page);

    // @todo Validate and submit callbacks.
  }

  /**
   * Determines the number of items to show on each page.
   *
   * @return int
   *   The configured number of items per page.
   */
  protected function getItemsPerPage() {
    if ($this->plugin->isConfigurable() && isset($this->plugin->getConfiguration()['items_per_page'])) {
      return $this->plugin->getConfiguration()['items_per_page'];
    }

    return 20;
  }

  /**
   * Adds item previews to the pane's form.
   *
   * @param array $form
   *   The current form to modify.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current form state.
   * @param array $query
   *   Current query parameters for link generation.
   */
  protected function addPreviews(array &$form, FormStateInterface $form_state, array $query) {
    $form['content'] = [
      '#theme' => 'media_library_pane_content',
      '#attributes' => [
        'class' => ['js-media-library-view-content'],
        'id' => 'media-library-extend-form-content',
      ],
    ];

    // @todo Allow plugin to specify a header message.
    // Add result count.
    $form['content']['result_count'] = [
      '#type' => 'item',
      '#title' => $this->formatPlural($this->resultCount,
        '1 items matches your search',
        '@count items match your search.'
      ),
      '#attributes' => [
        'class' => ['view-header'],
      ],
    ];
    if (is_null($this->resultCount)) {
      $form['content']['result_count']['#access'] = FALSE;
    }

    // Display result previews.
    $results = $this->plugin->getResults();
    foreach ($results as $result) {
      // @todo We will need different templates for other view modes.
      $form['content']['previews'][] = [
        '#theme' => 'media_library_item__small',
        '#attributes' => [
          'class' => [
            'js-media-library-item',
            'js-click-to-select',
          ],
        ],
        'select' => [
          '#type' => 'container',
          '#attributes' => [
            'class' => [
              'js-click-to-select-checkbox',
            ],
          ],
          'select_checkbox' => [
            '#type' => 'checkbox',
            '#title' => $this->t('Select @name', ['@name' => $result['label']]),
            '#title_display' => 'invisible',
            '#return_value' => 'mle:' . $this->pane->id() . ':' . $result['id'],
            // The checkbox's value is never processed by this form. It is
            // present for usability and accessibility reasons, and only used by
            // JavaScript to track whether or not this media item is selected.
            // The hidden 'current_selection' field is used to store the actual
            // IDs of selected media items.
            '#value' => FALSE,
          ],
        ],
        'rendered_entity' => [
          '#theme' => 'media_library_result_preview',
          '#result' => $result,
        ],
      ];
    }

    // The selection is persistent across different pages in the media library
    // and populated via JavaScript.
    // @see \Drupal\media_library\Plugin\views\field\MediaLibrarySelectForm
    $form['content']['form_selection'] = [
      '#type' => 'hidden',
      '#attributes' => [
        // This is used to identify the hidden field in the form via JavaScript.
        'id' => 'media-library-modal-selection',
        'name' => 'form_selection',
      ],
    ];
  }

  /**
   * Adds a pager to the pane's form.
   *
   * @param array $form
   *   The current form to modify.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current form state.
   * @param array $query
   *   Current query parameters for link generation.
   */
  protected function addPager(array &$form, FormStateInterface $form_state, array $query) {
    // Render the pager.
    $form['content']['pager'] = [
      '#type' => 'pager',
      '#parameters' => $query,
      '#route_name' => 'media_library.ui',
      '#theme_wrappers' => [
        'container' => [
          '#attributes' => [
            'class' => ['js-media-library-extend-pager'],
          ],
        ],
      ],
    ];

    if (is_null($this->resultCount)) {
      // Media library depends on views anyway, so we can hijack its mini pager.
      $form['content']['pager']['#theme'] = 'views_mini_pager';
      unset($form['content']['pager']['#type']);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    _media_library_extend_form_insert_validate($form, $form_state);
    parent::validateForm($form, $form_state);
  }

  /**
   * Stores the current plugin's state.
   *
   * @param array $form
   *   The current form to modify.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current form state.
   */
  protected function setPluginState(array $form, FormStateInterface $form_state) {
    $subform_state = SubformState::createForSubform($form['filters'], $form, $form_state);
    $values = $subform_state->getValues();
    $values['page'] = $this->page;
    $values['target_bundle'] = $this->target_bundle;
    $this->plugin->setValues($values);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // This space intentionally left blank.
  }

  /**
   * {@inheritdoc}
   */
  public function ajaxSubmitForm(array &$form, FormStateInterface $form_state, Request $request) {
    $field_id = $form_state->getTriggeringElement()['#field_id'];
    $selected_ids = explode(',', $form_state->getValue($field_id));

    // Allow the opener service to handle the selection.
    $state = MediaLibraryState::fromRequest($request);

    return $this->openerResolver->get($state)
      ->getSelectionResponse($state, $selected_ids)
      ->addCommand(new CloseDialogCommand());
  }

  /**
   * {@inheritdoc}
   */
  public function ajaxFilterForm(array &$form, FormStateInterface $form_state, Request $request) {
    $query = $request->query->all();
    $query[FormBuilderInterface::AJAX_FORM_REQUEST] = TRUE;
    $query['query'] = $form_state->getValue('query');
    $this->addFilters($form, $form_state, $query);
    $this->addPreviews($form, $form_state, $query);
    $this->addPager($form, $form_state, $query);

    return $form['content'];
  }

}
