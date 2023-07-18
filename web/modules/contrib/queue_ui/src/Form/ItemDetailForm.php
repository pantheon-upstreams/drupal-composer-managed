<?php

namespace Drupal\queue_ui\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\queue_ui\QueueUIManager;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class QueueUIInspectForm declaration.
 *
 * @package Drupal\queue_ui\Form
 */
class ItemDetailForm extends FormBase {

  /**
   * The QueueUIManager.
   *
   * @var \Drupal\queue_ui\QueueUIManager
   */
  private $queueUIManager;

  /**
   * InspectForm constructor.
   *
   * @param \Drupal\queue_ui\QueueUIManager $queueUIManager
   *   The QueueUIManager object.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The Renderer object.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The ModuleHandler object.
   */
  public function __construct(QueueUIManager $queueUIManager, RendererInterface $renderer, ModuleHandlerInterface $moduleHandler) {
    $this->queueUIManager = $queueUIManager;
    $this->renderer = $renderer;
    $this->moduleHandler = $moduleHandler;
  }

  /**
   * {@inheritdoc}
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The current service container.
   *
   * @return static
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.queue_ui'),
      $container->get('renderer'),
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'queue_ui_item_detail_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $queueName = FALSE, $queueItem = FALSE) {
    if ($queue_ui = $this->queueUIManager->fromQueueName($queueName)) {
      $queueItem = $queue_ui->loadItem($queueItem);

      $data = [
        '#type' => 'html_tag',
        '#tag' => 'pre' ,
        '#value' => print_r(unserialize($queueItem->data, ['allowed_classes' => FALSE]), TRUE),
      ];
      $data = $this->renderer->renderPlain($data);

      $rows = [
        'id' => [
          'data' => [
            'header' => $this->t('Item ID'),
            'data' => $queueItem->item_id,
          ],
        ],
        'queueName' => [
          'data' => [
            'header' => $this->t('Queue name'),
            'data' => $queueItem->name,
          ],
        ],
        'expire' => [
          'data' => [
            'header' => $this->t('Expire'),
            'data' => ($queueItem->expire ? date(DATE_RSS, $queueItem->expire) : $queueItem->expire),
          ],
        ],
        'created' => [
          'data' => [
            'header' => $this->t('Created'),
            'data' => date(DATE_RSS, $queueItem->created),
          ],
        ],
        'data' => [
          'data' => [
            'header' => [
              'data' => $this->t('Data'),
              'style' => 'vertical-align:top',
            ],
            'data' => $data,
          ],
        ],
      ];

      return [
        'table' => [
          '#type' => 'table',
          '#rows' => $rows,
        ],
      ];
    }
  }

  /**
   * {@inheritdoc}
   *
   * @param array $form
   *   The form where the settings form is being included in.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {}

}
