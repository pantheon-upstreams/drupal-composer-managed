<?php

namespace Drupal\hearsay_preview\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Annotation for post method
 *
 * @RestResource(
 *   id = "hs_preview_expired",
 *   label = @Translation("HS Delete Preview"),
 *   serialization_class = "",
 *   uri_paths = {
 *     "create" = "/api/delete_preview"
 *   }
 * )
 */
class ExpiredPreview extends ResourceBase
{
    /**
     * Responds to POST requests.
     *
     * @return \Drupal\rest\ResourceResponse
     *   The response will be token verification status.
     */

    /**
     * Constructs a Drupal\rest\Plugin\ResourceBase object.
     *
     * @param array $configuration
     *   A configuration array containing information about the plugin instance.
     * @param string $plugin_id
     *   The plugin_id for the plugin instance.
     * @param mixed $plugin_definition
     *   The plugin implementation definition.
     * @param array $serializer_formats
     *   The available serialization formats.
     * @param \Psr\Log\LoggerInterface $logger
     *   A logger instance.
     */
    public function __construct(
        array $configuration,
        $plugin_id,
        $plugin_definition,
        array $serializer_formats,
        LoggerInterface $logger
    ) {
        parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
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
            $container->getParameter('serializer.formats'),
            $container->get('logger.factory')->get('custom_rest')
        );
    }

    /**
     * Responds to POST requests.
     *
     * @return \Drupal\
     *   Delete the expired preview configuration
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *   Throws exception expected.
     */
    public function post()
    {
        $preview_token = PREVIEW_TOKEN;
        $headers = getallheaders();
        $xPreviewToken = $headers['X-Preview-Token'];
        //Verify token
        if ($xPreviewToken != $preview_token) {
            \Drupal::logger('expired_preview')->notice('Received bad token for new expired preview request for expired preview');
            $response_status['status'] = 401;
            $response_status['error_message'] = 'Received bad token for new preview request for expired preview';
            return new ResourceResponse(401);
        }
        //Log start of job
        \Drupal::logger('expired_preview')->notice('Receiving new preview request for Preview Deletion successfully');
        try {
            $response_status = $this->deleteExpired();
        } catch (\Exception $e) {
            \Drupal::logger('Sites Preview Expired post error')->error($e->getMessage() . ' <br> — Preview Expired ');
        }
        return new ResourceResponse($response_status);
    }

    /**
     * Returns the Expired status for the slug and removes the expired slugs.
     *
     * @return string
     *   Returns Term Status.
     */
    public function deleteExpired()
    {
        // Get all preview data
        $previewConfig = \Drupal::config('sites_preview.api');
        $items = $previewConfig->get();
        $current_time = \Drupal::time()->getCurrentTime();
        $dateFormatter = \Drupal::service('date.formatter');
        $currentDate = $dateFormatter->format($current_time, 'custom', 'Y-m-d H:i');
        $logArray = '';
        foreach ($items as $k => $item) {
            $ExpiredTime = '';
            if (isset($item['config_expiration'])) {
                $ExpiredTime = $dateFormatter->format($item['config_expiration'], 'custom', 'Y-m-d H:i');
            }
            $expired = $currentDate > $ExpiredTime ? true : false;
            if ($expired == true) {
                unset($items[$k]);
            }
            try {
                $connection = \Drupal::service('database');
                if (isset($items[$k])) {
                    $logArray = $this->t(
                        '
              @logs <br> Expiration details :<br> 
              Slug = @slug <br>
              Creation Time = @created <br>
              Expiration Time = @expiration <br>',
                        [
                            '@logs' => $logArray,
                            '@slug' => $items[$k]['slug'],
                            '@created' => $dateFormatter->format($item['config_created'], 'custom', 'Y-m-d H:i'),
                            '@expiration' => $dateFormatter->format($item['config_expiration'], 'custom', 'Y-m-d H:i'),
                        ]
                    );
                    $query = $connection->insert('deletion_preview_log')->fields([
                        'slug' => $items[$k]['slug'],
                        'CreatedTime' => $items[$k]['config_created'],
                        'ExpiredTime' =>  $items[$k]['config_expiration'],
                    ]);
                    $query->execute();
                }
            } catch (\Exception $e) {
                \Drupal::messenger()->addError($this->t('Expired preview data not inserted in database'));
            }
        }
        if ($logArray == '') {
            \Drupal::logger('expired_log')->notice("Preview Data is not available");
        } else {
            \Drupal::logger('expired_log')->notice($logArray);
        }
        $message = "The Preview deletion is successfully";
        return  $message;
    }

    /**
     * Used to set the cache time of a page.
     *
     * @return void
     *   Return 0.
     */
    public function permissions()
    {
        return [];
    }
}
