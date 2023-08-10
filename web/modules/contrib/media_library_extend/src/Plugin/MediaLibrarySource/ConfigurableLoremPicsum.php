<?php

namespace Drupal\media_library_extend\Plugin\MediaLibrarySource;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Utility\Token;
use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a media library pane to pull placeholder images from lorem.picsum.
 *
 * @MediaLibrarySource(
 *   id = "configurable_lorem_picsum",
 *   label = @Translation("Configurable Lorem Picsum"),
 *   source_types = {
 *     "image",
 *   },
 * )
 */
class ConfigurableLoremPicsum extends MediaLibrarySourceBase {

  /**
   * The http client.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('token'),
      $container->get('file_system'),
      $container->get('http_client')
    );
  }

  /**
   * Constructs a new LoremPicsum object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Utility\Token $token
   *   The token service.
   * @param \GuzzleHttp\Client $http_client
   *   The HTTP client.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, Token $token, FileSystemInterface $file_system, Client $http_client) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_type_manager, $token, $file_system);
    $this->httpClient = $http_client;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'grayscale' => FALSE,
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['grayscale'] = [
      '#type' => 'checkbox',
      '#title' => t('Grayscale'),
      '#default_value' => $this->configuration['grayscale'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getResults() {
    $response = $this->httpClient->request('GET', 'https://picsum.photos/v2/list', [
      'headers' => [
          // @todo Check if we need this header.
        'User-Agent' => 'Mozilla/5.0',
      ],
      'query' => [
        'page' => $this->getValue('page') + 1,
        'limit' => $this->configuration['items_per_page'],
      ],
    ]);

    // @todo Error handling.
    $images = json_decode((string) $response->getBody(), TRUE);
    $grayscale = $this->configuration['grayscale'];

    $results = [];
    foreach ($images as $image) {
      $height = floor(200 * $image['height'] / $image['width']);

      $results[] = [
        'id' => $image['id'],
        'label' => $image['author'],
        'preview' => [
          '#type' => 'html_tag',
          '#tag' => 'img',
          '#attributes' => [
            'src' => 'https://picsum.photos/id/' . $image['id'] . '/200/' . $height . ($grayscale ? '?grayscale' : ''),
            'alt' => $image['author'],
            'title' => $image['author'],
          ],
        ],
      ];
    }

    return $results;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityId($selected_id) {
    $response = $this->httpClient->request('GET', 'https://picsum.photos/id/' . $selected_id . '/info', [
      'headers' => [
          // @todo Check if we need this header.
        'User-Agent' => 'Mozilla/5.0',
      ],
    ]);

    // @todo Error handling.
    $info = json_decode((string) $response->getBody(), TRUE);

    $grayscale = $this->configuration['grayscale'];
    $url = 'https://picsum.photos/id/' . $selected_id . '/' . $info['width'] . '/' . $info['height'] . ($grayscale ? '?grayscale' : '');

    // Create a media entity.
    $entity = $this->createEntityStub('Lorem Picsum - ' . $selected_id);

    // Download the requested file.
    try {
      $response = $this->httpClient->request('GET', $url, [
        'timeout' => 30,
      ]);

      if ($response->getStatusCode() != 200) {
        // @todo Error handling.
        return NULL;
      }

      // Get file extension from response, since the selected download profile
      // might use a different extension than the original file.
      $filename = $selected_id . '.jpg';

      // Save to filesystem.
      $file = file_save_data($response->getBody(), $this->getUploadLocation() . '/' . $filename);

      // Attach file to media entity.
      $source_field = $this->getSourceField();
      $entity->{$source_field}->target_id = $file->id();
      $entity->{$source_field}->alt = t('Placeholder image by @author', [
        '@author' => $info['author'],
      ]);
      $entity->save();

      return $entity->id();
    }
    catch (TransferException $e) {
      // @todo Error handling.
    }
  }

}
