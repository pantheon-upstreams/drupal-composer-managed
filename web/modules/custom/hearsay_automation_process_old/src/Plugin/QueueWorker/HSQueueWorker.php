<?php

namespace Drupal\hearsay_automation_process\Plugin\QueueWorker;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;

/**
 * Create / Update / Delete queue item from Taxonomy And Nodes.
 *
 * @QueueWorker(
 *   id = "hs_queue_processing",
 *   title = @Translation("HS Import Processing Queue"),
 *   cron = {"time" = 180}
 * )
 */
class HSQueueWorker extends QueueWorkerBase implements ContainerFactoryPluginInterface
{
    /**
     * Drupal\Core\Entity\EntityTypeManagerInterface definition.
     *
     * @var \Drupal\Core\Entity\EntityTypeManagerInterface
     */
    private $entityTypeManager;

    /**
     * Drupal\Core\Logger\LoggerChannelFactoryInterface definition.
     *
     * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
     */
    private $loggerChannelFactory;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        array $configuration,
        $plugin_id,
        $plugin_definition,
        EntityTypeManagerInterface $entityTypeManager,
        LoggerChannelFactoryInterface $loggerChannelFactory
    ) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->entityTypeManager = $entityTypeManager;
        $this->loggerChannelFactory = $loggerChannelFactory;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
    {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('entity_type.manager'),
            $container->get('logger.factory')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function processItem($item)
    {
        $objHSUtility = \Drupal::service('hearsay_automation_process_service.utility');
        switch ($item['processAction']) {
            case 'CREATE-UPDATE':
                // Create taxonomy and nodes.
                $objHSUtility->logMessage .= 'Item created -  ' . $item['baseSlug'] . '</br>';
                $objHSUtility->createOrUpdateBaseSlugAndNodes($item);
                break;

            case 'DELETE':
                // Delete taxonomy and nodes.
                $accountId = $item['accountId'];
                $objHSUtility->deleteBaseSlugAndNodes($accountId);
                break;

            default:
                $objHSUtility->logMessage .= 'Wrong processAction set to ' . $item['baseSlug'];
                $objHSUtility->logMessage .= ' So unable to process it in Queue. <br/>';
                break;
        }
    }
}
