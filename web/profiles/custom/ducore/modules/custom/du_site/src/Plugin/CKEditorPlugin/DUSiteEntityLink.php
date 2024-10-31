<?php

namespace Drupal\du_site\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\editor\Entity\Editor;

/**
 * Defines the "entitylink" plugin.
 *
 * @CKEditorPlugin(
 *   id = "entitylink",
 *   label = @Translation("Entity link"),
 * )
 */
class DUSiteEntityLink extends CKEditorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return \Drupal::service('extension.list.module')->getPath('du_site') . '/js/entitylink_plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries(Editor $editor) {
    return [
      'core/drupal.ajax',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    return [
      'EntityLink_dialogTitleAdd' => t('Add Link'),
      'EntityLink_dialogTitleEdit' => t('Edit Link'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    $path = \Drupal::service('extension.list.module')->getPath('ckeditor_entity_link') . '/js/plugins/entitylink';

    return [
      'EntityLink' => [
        'label' => t('Link'),
        'image' => $path . '/link.png',
      ],
    ];
  }

}
