<?php

namespace Drupal\section_library\Controller;

use Drupal\Core\Ajax\AjaxHelperTrait;
use Drupal\Component\Uuid\UuidInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\layout_builder\SectionStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\section_library\Entity\SectionLibraryTemplate;
use Drupal\layout_builder\Controller\LayoutRebuildTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\layout_builder\LayoutTempstoreRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\section_library\DeepCloningTrait;
use Drupal\layout_builder\Section;

/**
 * Defines a controller to import a section.
 *
 * @internal
 *   Controller classes are internal.
 */
class ImportSectionFromLibraryController implements ContainerInjectionInterface {

  use AjaxHelperTrait;
  use LayoutRebuildTrait;
  use DeepCloningTrait;

  /**
   * The layout tempstore repository.
   *
   * @var \Drupal\layout_builder\LayoutTempstoreRepositoryInterface
   */
  protected $layoutTempstoreRepository;

  /**
   * The UUID generator.
   *
   * @var \Drupal\Component\Uuid\UuidInterface
   */
  protected $uuidGenerator;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * AddSectionController constructor.
   *
   * @param \Drupal\layout_builder\LayoutTempstoreRepositoryInterface $layout_tempstore_repository
   *   The layout tempstore repository.
   * @param \Drupal\Component\Uuid\UuidInterface $uuid
   *   The UUID generator.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(LayoutTempstoreRepositoryInterface $layout_tempstore_repository, UuidInterface $uuid, EntityTypeManagerInterface $entity_type_manager) {
    $this->layoutTempstoreRepository = $layout_tempstore_repository;
    $this->uuidGenerator = $uuid;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('layout_builder.tempstore_repository'),
      $container->get('uuid'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * Provides the UI for choosing a new block.
   *
   * @param int $section_library_id
   *   The entity id.
   * @param \Drupal\layout_builder\SectionStorageInterface $section_storage
   *   The section storage.
   * @param int $delta
   *   The delta of the section to splice.
   *
   * @return array
   *   A render array.
   */
  public function build($section_library_id, SectionStorageInterface $section_storage, $delta) {
    $section_library_template = SectionLibraryTemplate::load($section_library_id);
    $sections = $section_library_template->get('layout_section')->getValue();
    if ($sections) {
      $reversed_sections = array_reverse($sections);
      foreach ($reversed_sections as $section) {
        $current_section = $section['section'];
        $current_section_array = $current_section->toArray();

        // Clone section.
        $cloned_section = new Section(
          $current_section->getLayoutId(),
          $current_section->getLayoutSettings(),
          $current_section->getComponents(),
          $current_section_array['third_party_settings']
        );

        // Replace section components with new instances.
        $deep_cloned_section = $this->cloneAndReplaceSectionComponents($cloned_section);

        // Create a new section.
        $section_storage->insertSection($delta, $deep_cloned_section);
      }
    }

    $this->layoutTempstoreRepository->set($section_storage);

    if ($this->isAjax()) {
      return $this->rebuildAndClose($section_storage);
    }
    else {
      $url = $section_storage->getLayoutBuilderUrl();
      return new RedirectResponse($url->setAbsolute()->toString());
    }
  }

}
