<?php

namespace Drupal\layout_builder_browser\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form handler for the block categories add and edit forms.
 */
class BlockCategoryForm extends EntityForm {

  /**
   * Constructs an layout_builder_browserForm object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entityTypeManager.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    /** @var \Drupal\layout_builder_browser\Entity\LayoutBuilderBrowserBlockCategory $layout_builder_browser */
    $layout_builder_browser = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $layout_builder_browser->label(),
      '#description' => $this->t("Label for the block category."),
      '#required' => TRUE,
    ];
    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $layout_builder_browser->id(),
      '#machine_name' => [
        'exists' => [$this, 'exist'],
      ],
      '#disabled' => !$layout_builder_browser->isNew(),
    ];

    $form['status'] = [
      '#title' => $this->t('Enabled'),
      '#type' => 'checkbox',
      '#default_value' => $layout_builder_browser->status(),
    ];

    // You will need additional form elements for your custom properties.
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $layout_builder_browser = $this->entity;
    $status = $layout_builder_browser->save();

    if ($status) {
      $this->messenger()->addMessage($this->t('Saved the %label layout_builder_browser.', [
        '%label' => $layout_builder_browser->label(),
      ]));
    }
    else {
      $this->messenger()->addMessage($this->t('The %label layout_builder_browser was not saved.', [
        '%label' => $layout_builder_browser->label(),
      ]), MessengerInterface::TYPE_ERROR);
    }

    $form_state->setRedirect('entity.layout_builder_browser_blockcat.collection');
  }

  /**
   * Check whether an layout_builder_browser configuration entity exists.
   *
   * @var int $id
   *   The id of the block to check.
   *
   * @return bool
   *   True if block exists.
   */
  public function exist($id) {
    $entity = $this->entityTypeManager->getStorage('layout_builder_browser_blockcat')->getQuery()
      ->accessCheck()
      ->condition('id', $id)
      ->execute();
    return (bool) $entity;
  }

}
