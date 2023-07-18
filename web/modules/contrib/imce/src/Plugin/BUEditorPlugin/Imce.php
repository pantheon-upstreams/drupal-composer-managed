<?php

namespace Drupal\imce\Plugin\BUEditorPlugin;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\editor\Entity\Editor;
use Drupal\bueditor\BUEditorPluginBase;
use Drupal\bueditor\Entity\BUEditorEditor;
use Drupal\imce\Imce as ImceMain;

/**
 * Defines Imce as a BUEditor plugin.
 *
 * @BUEditorPlugin(
 *   id = "imce",
 *   label = "Imce File Manager"
 * )
 */
class Imce extends BUEditorPluginBase {

  /**
   * {@inheritdoc}
   */
  // @codingStandardsIgnoreLine
  public function alterEditorJS(array &$js, BUEditorEditor $bueditor_editor, Editor $editor = NULL) {
    // Check selected file browser.
    if (isset($js['settings']['fileBrowser']) && $js['settings']['fileBrowser'] === 'imce') {
      // Check access.
      if (ImceMain::access()) {
        $js['libraries'][] = 'imce/drupal.imce.bueditor';
      }
      else {
        unset($js['settings']['fileBrowser']);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function alterEditorForm(array &$form, FormStateInterface $form_state, BUEditorEditor $bueditor_editor) {
    // Add imce option to file browser field.
    $fb = &$form['settings']['fileBrowser'];
    $fb['#options']['imce'] = $this->t('Imce File Manager');
    // Add configuration link.
    $form['settings']['imce'] = [
      '#type' => 'container',
      '#states' => [
        'visible' => [':input[name="settings[fileBrowser]"]' => ['value' => 'imce']],
      ],
      '#attributes' => [
        'class' => ['description'],
      ],
      'content' => [
        '#markup' => $this->t('Configure <a href=":url">Imce File Manager</a>.', [':url' => Url::fromRoute('imce.admin')->toString()]),
      ],
    ];
    // Set weight.
    if (isset($fb['#weight'])) {
      $form['settings']['imce']['#weight'] = $fb['#weight'] + 0.1;
    }
  }

}
