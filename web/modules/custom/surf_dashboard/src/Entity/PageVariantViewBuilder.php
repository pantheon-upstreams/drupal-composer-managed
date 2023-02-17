<?php

namespace Drupal\surf_dashboard\Entity;

use Drupal\Core\Datetime\Entity\DateFormat;
use Drupal\Core\Entity\EntityInterface;
use Drupal\page_manager\Entity\PageVariant;
use Drupal\page_manager\Entity\PageVariantViewBuilder as PageManagerPageVariantViewBuilder;

class PageVariantViewBuilder extends PageManagerPageVariantViewBuilder {

  public function view(EntityInterface $entity, $view_mode = 'full', $langcode = NULL) {
    $build = parent::view($entity, $view_mode, $langcode);
    $id = $entity->id();
    if (strpos($id, 'dashboard_') !== 0) {
      return $build;
    }
    return $this->addCustomThemeForDashboardPages($build, $entity, $view_mode, $langcode);
  }

  private function setUserDisplayName(&$build, PageVariant $entity) {
    $contexts = $entity->getContexts();
    if (!empty($contexts['user'])) {
      /** @var \Drupal\Core\Plugin\Context\Context $user_context */
      $user_context = $contexts['user'];
      $user = $user_context->getContextData()->getValue();
      $build['#user_display_name'] = $user->getDisplayName();
    }
  }

  private function getContextValue(PageVariant $entity, $context_name) {
    $contexts = $entity->getContexts();
    return !empty($contexts[$context_name]) ? $contexts[$context_name]->getContextData()->getValue() : NULL;
  }

  private function setNode(&$build, PageVariant $entity) {
    $node = NULL;
    if ($user_request = $this->getContextValue($entity, 'user_request')) {
      $node = $user_request->field_ref_event->entity;
    }

    if ($download_request = $this->getContextValue($entity, 'download_request')) {
      $node = $download_request->request_items->entity->download_item->entity->field_curriculum_module->entity;
    }
    if ($node) {
      $build['#node_title'] = $node->label();
      $build['#node'] = $node;
    }

  }

  private function addCustomThemeForDashboardPages($content, PageVariant $entity, $view_mode, $langcode) {
    $build['#theme'] = 'dashboard_page';
    $build['#content'] = $content + [
      '#type' => 'container'
      ];
    $build['#page_title'] = $entity->getPage()->label();

    $this->setUserDisplayName($build, $entity);
    $this->setNode($build, $entity);
    $build['#page_id'] = $entity->getPage()->id();

    return $build;
  }
}