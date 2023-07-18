<?php

namespace Drupal\layout_builder_blocks\Form;

use Drupal\bootstrap_styles\Form\StylesFilterConfigForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\bootstrap_styles\StylesGroup\StylesGroupManager;
use Drupal\Core\Block\BlockManagerInterface;

/**
 * Configure layout builder blocks styles.
 */
class StylesForm extends StylesFilterConfigForm {

  /**
   * The block manager.
   *
   * @var \Drupal\Core\Block\BlockManagerInterface
   */
  protected $blockManager;

  /**
   * Constructs a StylesFilterConfigForm object.
   *
   * @param \Drupal\bootstrap_styles\StylesGroup\StylesGroupManager $styles_group_manager
   *   The styles group plugin manager.
   * @param \Drupal\Core\Block\BlockManagerInterface $blockManager
   *   The block manager.
   */
  public function __construct(StylesGroupManager $styles_group_manager, BlockManagerInterface $blockManager) {
    parent::__construct($styles_group_manager);
    $this->blockManager = $blockManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.bootstrap_styles_group'),
      $container->get('plugin.manager.block')
    );
  }

  /**
   * Config name.
   *
   * @var string
   */
  const CONFIG = 'layout_builder_blocks.styles';

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $block_restrictions = [];
    $config = $this->configFactory->getEditable(static::CONFIG);
    if ($config->get('block_restrictions')) {
      $block_restrictions = $config->get('block_restrictions');
    }

    $blockDefinitions = $this->blockManager->getDefinitions();
    $blockDefinitions = $this->blockManager->getGroupedDefinitions($blockDefinitions);

    // Remove individual reusable blocks from list.
    unset($blockDefinitions['Custom']);

    if (isset($blockDefinitions['Inline blocks'])) {
      // Relabel the inline block type listing as generic "Custom block types".
      // This category will apply to inline blocks & reusable blocks.
      $blockDefinitions['Custom block types'] = $blockDefinitions['Inline blocks'];
      unset($blockDefinitions['Inline blocks']);
      ksort($blockDefinitions);
    }

    $form['block_restrictions'] = [
      '#type' => 'details',
      '#title' => $this->t('Block restrictions'),
      '#description' => $this->t('Optionally limit this style to the following blocks.'),
    ];

    foreach ($blockDefinitions as $category => $blocks) {
      $category_form = [
        '#type' => 'details',
        '#title' => $category,
        '#collapsible' => TRUE,
        '#collapsed' => TRUE,
      ];
      foreach ($blocks as $blockId => $block) {
        $machine_name = $blockId;
        $category_form[$blockId] = [
          '#type' => 'checkbox',
          '#title' => $block['admin_label'] . ' <small>(' . $machine_name . ')</small>',
          '#default_value' => in_array($blockId, $block_restrictions),
          '#parents' => [
            'block_restrictions',
            $blockId,
          ],
        ];
        if ($category == 'Custom block types') {
          $machine_name = str_replace('inline_block:', '', $machine_name);
          $category_form[$blockId]['#title'] = $block['admin_label'] . ' <small>(' . $machine_name . ')</small>';
          $category_form[$blockId]['#description'] = $this->t('Block type selections effect both re-usable and inline blocks.');
        }
      }
      $form['block_restrictions'][$category] = $category_form;
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $blockRestrictions = $form_state->getValue('block_restrictions');
    $blockRestrictions = array_keys(array_filter($blockRestrictions));

    $config = $this->configFactory->getEditable(static::CONFIG);
    $config->set('block_restrictions', $blockRestrictions);
    $config->save();
  }

}
