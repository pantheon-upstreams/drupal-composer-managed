<?php

namespace Drupal\du_site\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a Copyright block.
 *
 * @Block(
 *   id = "du_copyright_block",
 *   admin_label = @Translation("DU Copyright Block"),
 *   category = @Translation("DU Site Blocks")
 *
 * )
 */
class DuCopyrightBlock extends BlockBase implements BlockPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    $form['copyright_block_body'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Body'),
      '#format' => 'rich_text',
      '#default_value' => isset($config['copyright_block_body']['value']) ? $config['copyright_block_body']['value'] :
      '<p class="copyright">Copyright ©[current-date:html_year] | All Rights Reserved | Equal Opportunity Affirmative action institution</p>',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['copyright_block_body'] = $values['copyright_block_body'];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    return [
      '#markup' => check_markup($config['copyright_block_body']['value'], $config['copyright_block_body']['format']),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $default_config = \Drupal::config('du_site.settings');
    return [
      'copyright_block_body' => $default_config->get('copyright_block_body'),
    ];
  }

}
