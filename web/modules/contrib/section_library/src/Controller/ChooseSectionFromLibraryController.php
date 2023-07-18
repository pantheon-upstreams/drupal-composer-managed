<?php

namespace Drupal\section_library\Controller;

use Drupal\Core\Ajax\AjaxHelperTrait;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ExtensionList;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\layout_builder\Context\LayoutBuilderContextTrait;
use Drupal\layout_builder\LayoutBuilderHighlightTrait;
use Drupal\layout_builder\SectionStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\section_library\Entity\SectionLibraryTemplate;
use Drupal\Core\Render\Markup;

/**
 * Defines a controller to choose a section from library.
 *
 * @internal
 *   Controller classes are internal.
 */
class ChooseSectionFromLibraryController implements ContainerInjectionInterface {

  use AjaxHelperTrait;
  use LayoutBuilderContextTrait;
  use LayoutBuilderHighlightTrait;
  use StringTranslationTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The extension list module service.
   *
   * @var \Drupal\Core\Extension\ExtensionList
   */
  protected $extensionListModule;

  /**
   * ChooseSectionFromLibraryController constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Extension\ExtensionList $extension_list_module
   *   The extension list module service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ExtensionList $extension_list_module) {
    $this->entityTypeManager = $entity_type_manager;
    $this->extensionListModule = $extension_list_module;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('extension.list.module')
    );
  }

  /**
   * Provides the UI for choosing a new block.
   *
   * @param \Drupal\layout_builder\SectionStorageInterface $section_storage
   *   The section storage.
   * @param int $delta
   *   The delta of the section to splice.
   *
   * @return array
   *   A render array.
   */
  public function build(SectionStorageInterface $section_storage, $delta) {
    $build['filter'] = [
      '#type' => 'search',
      '#title' => $this->t('Filter by section label'),
      '#title_display' => 'invisible',
      '#size' => 30,
      '#placeholder' => $this->t('Filter by section label'),
      '#attributes' => [
        'class' => [
          'section-library-filter',
          'js-layout-builder-section-library-filter',
        ],
        'title' => $this->t('Enter a part of the section label to filter by.'),
      ],
    ];

    $build['sections'] = $this->getSectionLinks($section_storage, $delta);
    return $build;
  }

  /**
   * Gets a render array of section links.
   *
   * @param \Drupal\layout_builder\SectionStorageInterface $section_storage
   *   The section storage.
   * @param int $delta
   *   The region the section is going in.
   *
   * @return array
   *   The section links render array.
   */
  protected function getSectionLinks(SectionStorageInterface $section_storage, $delta) {
    $sections = SectionLibraryTemplate::loadMultiple();
    $links = [];
    foreach ($sections as $section_id => $section) {
      $attributes = $this->getAjaxAttributes();
      $attributes['class'][] = 'js-layout-builder-section-library-link';
      // Default library image.
      $img_path = $this->extensionListModule->getPath('section_library') . '/images/default.png';
      if ($fid = $section->get('image')->target_id) {
        $file = $this->entityTypeManager->getStorage('file')->load($fid);
        $img_path = $file->getFileUri();
      }

      $icon_url = \Drupal::service('file_url_generator')->generateString($img_path);
      $link = [
        'title' => Markup::create('<img src="' . $icon_url . '" class="section-library-link-img" /> ' . '<span class="section-library-link-label">' . $section->label() . '</span>'),
        'url' => Url::fromRoute('section_library.import_section_from_library',
          [
            'section_library_id' => $section_id,
            'section_storage_type' => $section_storage->getStorageType(),
            'section_storage' => $section_storage->getStorageId(),
            'delta' => $delta,
          ]
        ),
        'attributes' => $attributes,
      ];

      $links[] = $link;
    }
    return [
      '#theme' => 'links',
      '#links' => $links,
      '#attributes' => [
        'class' => [
          'section-library-links',
        ],
      ],
    ];
  }

  /**
   * Get dialog attributes if an ajax request.
   *
   * @return array
   *   The attributes array.
   */
  protected function getAjaxAttributes() {
    if ($this->isAjax()) {
      return [
        'class' => ['use-ajax'],
        'data-dialog-type' => 'dialog',
        'data-dialog-renderer' => 'off_canvas',
      ];
    }
    return [];
  }

}
