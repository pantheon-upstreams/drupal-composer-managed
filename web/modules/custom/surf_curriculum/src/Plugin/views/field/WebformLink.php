<?php

namespace Drupal\surf_curriculum\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\Plugin\views\field\LinkBase;
use Drupal\views\ResultRow;
use Drupal\webform\Entity\Webform;

/**
 * Provides Webform link field handler.
 *
 * @ViewsField("surf_curriculum_webform_link")
 *
 * @DCG
 * The plugin needs to be assigned to a specific table column through
 * hook_views_data() or hook_views_data_alter().
 * For non-existent columns (i.e. computed fields) you need to override
 * self::query() method.
 */
class WebformLink extends LinkBase {

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['webform_id'] = ['default' => ''];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
    $webform_id = $this->options['webform_id'] ?? FALSE;
    $form['webform_id'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'webform',
      '#title' => $this->t('Webform ID'),
      '#default_value' => $this->getDefaultValue(),
    ];
  }

  private function getDefaultValue() {
    if (empty($this->options['webform_id'])) {
      return;
    }
    return Webform::load($this->options['webform_id']);
  }

  /**
   * {@inheritdoc}
   */
  protected function getUrlInfo(ResultRow $row) {
    $entity = $this->getEntity($row);
    if ($entity->getEntityTypeId() !== 'node') {
      return;
    }
    if ($entity->bundle() !== 'curriculum_module') {
      return;
    }
    return $entity->getWebformUrl();
  }
}
