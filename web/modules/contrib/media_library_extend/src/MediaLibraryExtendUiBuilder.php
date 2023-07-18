<?php

namespace Drupal\media_library_extend;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormState;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\media_library\MediaLibraryState;
use Drupal\media_library\MediaLibraryUiBuilder;
use Drupal\media_library\OpenerResolverInterface;
use Drupal\views\ViewExecutableFactory;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Service decorator which builds the media library with extension support.
 */
class MediaLibraryExtendUiBuilder extends MediaLibraryUiBuilder {

  /**
   * Constructs a MediaLibraryExtendUiBuilder instance.
   *
   * @param \Drupal\media_library\MediaLibraryUiBuilder $inner_service
   *   The media library ui builder.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   * @param \Drupal\views\ViewExecutableFactory $views_executable_factory
   *   The views executable factory.
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   The currently active request object.
   * @param \Drupal\media_library\OpenerResolverInterface $opener_resolver
   *   The opener resolver.
   */
  public function __construct(MediaLibraryUiBuilder $inner_service, EntityTypeManagerInterface $entity_type_manager, RequestStack $request_stack, ViewExecutableFactory $views_executable_factory, FormBuilderInterface $form_builder, OpenerResolverInterface $opener_resolver = NULL) {
    // @todo Add class attributes.
    $this->innerService = $inner_service;
    parent::__construct($entity_type_manager, $request_stack, $views_executable_factory, $form_builder, $opener_resolver);
  }

  /**
   * {@inheritdoc}
   */
  public static function dialogOptions() {
    return $this->innerService->dialogOptions();
  }

  /**
   * {@inheritdoc}
   */
  protected function buildLibraryContent(MediaLibraryState $state) {
    if ($state->get('media_library_extend') !== '1') {
      return parent::buildLibraryContent($state);
    }

    // After the form to add new media is submitted, we need to rebuild the
    // media library with a new instance of the media add form. The form API
    // allows us to do that by forcing empty user input.
    // @see \Drupal\Core\Form\FormBuilder::doBuildForm()
    $form_state = new FormState();
    if ($state->get('_media_library_form_rebuild')) {
      $form_state->setUserInput([]);
      $state->remove('_media_library_form_rebuild');
    }
    $form_state->set('media_library_state', $state);
    $pane = $this->entityTypeManager->getStorage('media_library_pane')->load($state->get('pane_id'));
    $form_state->set('pane', $pane);
    $form_state->set('source_plugin', $pane->getPlugin());
    $form = $this->formBuilder->buildForm('\Drupal\media_library_extend\Form\PaneSelectForm', $form_state);

    return [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'id' => 'media-library-content',
        'class' => ['media-library-content'],
        'tabindex' => -1,
      ],
      'view' => $form,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function checkAccess(AccountInterface $account, MediaLibraryState $state = NULL) {
    return $this->innerService->checkAccess($account, $state);
  }

  /**
   * {@inheritdoc}
   */
  protected function buildMediaTypeMenu(MediaLibraryState $state) {
    $allowed_types = $state->getAllowedTypeIds();
    if (count($allowed_types) === 1) {
      // @todo Exit early if no panes are configured.
      // @todo Propose refactor of buildMediaTypeMenu upstream to make this
      // less hacky.
      $allowed_type = key($allowed_types);
      $forced_tabs_types = $allowed_types + ['' => $allowed_type];
      $state->set('media_library_allowed_types', $forced_tabs_types);
      $menu = $this->innerService->buildMediaTypeMenu($state);
      $state->set('media_library_allowed_types', $allowed_types);
    }
    else {
      $menu = $this->innerService->buildMediaTypeMenu($state);
    }

    $panes = $this->entityTypeManager->getStorage('media_library_pane')
      ->loadByProperties(['bundle' => $allowed_types]);
    foreach ($panes as $pane_id => $pane) {
      $bundle = $pane->getTargetBundle();
      $plugin = $pane->getPlugin();
      $link_state = MediaLibraryState::create($state->getOpenerId(), $allowed_types, $pane->getTargetBundle(), $state->getAvailableSlots(), $state->getOpenerParameters());
      // Add the 'media_library_content' parameter so the response will
      // contain only the updated content for the tab.
      // @see self::buildUi()
      $link_state->set('media_library_content', 1);

      // Add the 'media_library_extend' parameter so the response will contain
      // the alternative media listing.
      $link_state->set('media_library_extend', 1);
      // @todo Set pane instead.
      $link_state->set('pane_id', $pane_id);

      $title = $pane->label();
      $display_title = [
        '#markup' => $this->t('<span class="visually-hidden">Show </span>@title<span class="visually-hidden"> media</span>', [
          '@title' => $title,
        ]),
      ];
      if ($state->get('media_library_extend') === '1') {
        $display_title = [
          '#markup' => $this->t('<span class="visually-hidden">Show </span>@title<span class="visually-hidden"> media</span><span class="active-tab visually-hidden"> (selected)</span>', [
            '@title' => $title,
          ]),
        ];
      }

      $menu['#links']['media-library-extend-' . $pane_id] = [
        'title' => $display_title,
        'url' => Url::fromRoute('media_library.ui', [], [
          'query' => $link_state->all(),
        ]),
        'attributes' => [
          'class' => ['media-library-menu__link'],
          'role' => 'button',
          'data-title' => $title,
        ],
      ];
    }

    // @todo Rewrite active class on existing menu item.
    return $menu;
  }

}
