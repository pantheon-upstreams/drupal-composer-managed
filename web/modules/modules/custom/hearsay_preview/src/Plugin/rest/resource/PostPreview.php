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
 *   id = "hs_preview_data_post",
 *   label = @Translation("HS Site changes preview"),
 *   serialization_class = "",
 *   uri_paths = {
 *     "create" = "/api/preview"
 *   }
 * )
 */
class PostPreview extends ResourceBase
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
     * {@inheritdoc}
     */
    protected function getEditableConfigNames()
    {
        return ['hearsay_preview.slug'];
    }

    /**
     * Responds to POST requests.
     *
     * @return \Drupal\
     *   Saves the preview configuration
     *   Returns a list of bundles for specified entity.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *   Throws exception expected.
     */
    public function post($data)
    {
        $preview_token = PREVIEW_TOKEN;

        if (!isset($data)) {
            $response_status['status'] = false;
        } else {
            $headers = getallheaders();
            $xPreviewToken = $headers['X-Preview-Token'];
            $host = \Drupal::request()->getSchemeAndHttpHost();
            $slug = $data['slug'];

            // Verify token
            if ($xPreviewToken != $preview_token) {
                \Drupal::logger('sites_preview')->notice('Received bad token for new preview request for "' . $slug);
                $response_status['status'] = 401;
                $response_status['error_message'] = 'Received bad token for new preview request for "' . $slug;
                return new ResourceResponse(401);
            }

            // Log start of job
            \Drupal::logger('sites_preview')->notice('Receiving new preview request for: " ' . $slug . '". <br> Returned new path successfully');

            // set created and expiration values
            $data['config_created'] = time();
            $data['config_expiration'] = time() + 600;

            try {
                $config = \Drupal::configFactory()->getEditable('sites_preview.api');
                $config->set($slug, $data)->save();

                $response_status['preview_path'] = $host . '/preview?slug=' . $slug;
            } catch (\Exception $e) {
                \Drupal::logger('Sites Preview post error')->error($e->getMessage() . ' <br> — Slug ' . $slug);
            }
        }
        return new ResourceResponse($response_status);
    }

    /**
     * Open access (using auth token check)
     *
     * @return void
     *   Return Null.
     */
    public function permissions()
    {
        return [];
    }
}
