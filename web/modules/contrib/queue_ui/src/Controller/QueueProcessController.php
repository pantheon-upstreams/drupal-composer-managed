<?php

namespace Drupal\queue_ui\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Controller for the queue process route.
 */
class QueueProcessController implements ContainerInjectionInterface {

  /**
   * The QueueUIBatchInterface.
   *
   * @var \Drupal\queue_ui\QueueUIBatchInterface
   */
  protected $batch;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = new static();
    $instance->batch = $container->get('queue_ui.batch');

    return $instance;
  }

  /**
   * Process a certain queue.
   */
  public function process(string $queueName): ?RedirectResponse {
    $this->batch->batch([$queueName]);

    return batch_process('<front>');
  }

  /**
   * Checks access for processing a certain queue.
   */
  public function access(AccountProxyInterface $account, string $queueName): AccessResultInterface {
    return AccessResult::allowedIfHasPermission($account, sprintf('process %s queue', $queueName));
  }

}
