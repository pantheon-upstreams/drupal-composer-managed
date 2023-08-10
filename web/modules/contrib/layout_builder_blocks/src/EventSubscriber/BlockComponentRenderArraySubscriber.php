<?php

namespace Drupal\layout_builder_blocks\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\layout_builder\Event\SectionComponentBuildRenderArrayEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\layout_builder\LayoutBuilderEvents;
use Drupal\bootstrap_styles\StylesGroup\StylesGroupManager;

/**
 * Class BlockComponentRenderArraySubscriber.
 */
class BlockComponentRenderArraySubscriber implements EventSubscriberInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Drupal\Core\Config\ConfigFactoryInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * BlockComponentRenderArraySubscriber constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Access configuration.
   * @param \Drupal\bootstrap_styles\StylesGroup\StylesGroupManager $styles_group_manager
   *   The styles group plugin manager.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, ConfigFactoryInterface $config_factory, StylesGroupManager $styles_group_manager) {
    $this->entityTypeManager = $entityTypeManager;
    $this->configFactory = $config_factory;
    $this->stylesGroupManager = $styles_group_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[LayoutBuilderEvents::SECTION_COMPONENT_BUILD_RENDER_ARRAY] = ['onBuildRender', 50];
    return $events;
  }

  /**
   * Add each component's block styles to the render array.
   *
   * @param \Drupal\layout_builder\Event\SectionComponentBuildRenderArrayEvent $event
   *   The section component render event.
   */
  public function onBuildRender(SectionComponentBuildRenderArrayEvent $event) {
    $build = $event->getBuild();

    $block_style_configs = [];
    if ($bootstrap_styles = $event->getComponent()->get('bootstrap_styles')) {
      if (isset($bootstrap_styles['block_style'])) {
        $block_style_configs = $bootstrap_styles['block_style'];
      }
    }

    // Build dynamic styles.
    if (count($block_style_configs) > 0) {
      $build = $this->stylesGroupManager->buildStyles(
        $build,
        // Storage.
        $block_style_configs,
        // Theme wrapper that we need to apply styles to it.
        NULL
      );
    }

    $event->setBuild($build);
  }

}
