<?php

namespace Drupal\queue_ui;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides dynamic permissions for the queue_ui module.
 */
class QueueUIPermissions implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * The QueueWorkerManager.
   *
   * @var \Drupal\Core\Queue\QueueWorkerManager
   */
  private $queueWorkerManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = new static();
    $instance->queueWorkerManager = $container->get('plugin.manager.queue_worker');

    return $instance;
  }

  /**
   * A permissions callback.
   *
   * @see queue_ui.permissions.yml
   *
   * @return array
   *   An array of permissions for all queues.
   */
  public function permissions(): array {
    $permissions = [];

    $queues = $this->queueWorkerManager->getDefinitions();
    foreach (array_keys($queues) as $queue_name) {
      $permissions += [
        sprintf('process %s queue', $queue_name) => [
          'title' => $this->t('Process %queue_name queue', ['%queue_name' => $queue_name]),
          'description' => $this->t('Initiate processing of the items in the %queue_name queue.', ['%queue_name' => $queue_name]),
        ],
      ];
    }

    return $permissions;
  }

}
