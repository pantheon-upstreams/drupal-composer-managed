<?php

namespace Drupal\media_library_extend\Plugin\MediaLibrarySource;

use Drupal\Component\Plugin\ConfigurableInterface;
use Drupal\Component\Render\PlainTextOutput;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\Core\Utility\Token;
use Drupal\media_library_extend\Plugin\MediaLibrarySourceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\File\FileSystemInterface;

/**
 * Provides a base class for media library panes to inherit from.
 */
abstract class MediaLibrarySourceBase extends PluginBase implements ContainerFactoryPluginInterface, MediaLibrarySourceInterface, ConfigurableInterface, PluginFormInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The token service.
   *
   * @var \Drupal\Core\Utility\Token
   */
  protected $token;

  /**
   * The target bundle for this plugin.
   *
   * @var string
   */
  protected $targetBundle;

  /**
   * The file system service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * Creates a new MediaLibrarySourceBase instance.
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
   * @param Drupal\Core\File\FileSystemInterface $fileSystem
   *   The file system service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, Token $token, FileSystemInterface $file_system) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->setConfiguration($configuration);
    $this->entityTypeManager = $entity_type_manager;
    $this->token = $token;
    $this->fileSystem = $file_system;
  }

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
      $container->get('file_system')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'items_per_page' => 20,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return $this->configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    $this->configuration = NestedArray::mergeDeep(
      $this->defaultConfiguration(),
      $configuration
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['items_per_page'] = [
      '#title' => $this->t('Items per page'),
      '#description' => $this->t('The number of result items to show on each page.'),
      '#type' => 'number',
      '#min' => 1,
      '#max' => 50,
      '#default_value' => $this->configuration['items_per_page'],
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    // Do nothing.
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $values = array_intersect_key($form_state->getValues(), $this->configuration);
    foreach ($values as $config_key => $config_value) {
      $this->configuration[$config_key] = $config_value;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    return $this->pluginDefinition['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary() {
    return [
      '#theme' => 'item_list',
      '#list_type' => 'ul',
      // '#empty' => $this->t('- None -'),
      '#items' => [
        $this->formatPlural($this->configuration['items_per_page'], '@count item per page', '@count items per page'),
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function setValues(array $values) {
    $this->values = $values;
  }

  /**
   * {@inheritdoc}
   */
  public function setValue(string $key, $value) {
    return $this->values[$key] = $value;
  }

  /**
   * {@inheritdoc}
   */
  public function getValue(string $key) {
    return $this->values[$key] ?? NULL;
  }

  /**
   * Sets the entity bundle that should be returned by this plugin.
   *
   * @param string $bundle
   *   The target bundle id for this plugin.
   */
  public function setTargetBundle(string $bundle) {
    $this->targetBundle = $bundle;
  }

  /**
   * Gets the entity bundle that should be returned by this plugin.
   *
   * @return string
   *   The target bundle id for this plugin.
   */
  public function getTargetBundle() {
    return $this->targetBundle;
  }

  /**
   * {@inheritdoc}
   */
  public function getCount() {
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array &$form, FormStateInterface $form_state) {
    return $form;
  }

  /**
   * Creates a new media entity to store data in.
   *
   * @return \Drupal\media\MediaInterface
   *   The prepared media entity.
   */
  protected function createEntityStub($title) {
    $bundle_id = $this->getTargetBundle();
    $entityTypeDefinition = $this->entityTypeManager->getDefinition('media');
    $entityClass = $entityTypeDefinition->getClass();
    $entity = $entityClass::create([
      $entityTypeDefinition->getKey('label') => $title,
      $entityTypeDefinition->getKey('bundle') => $bundle_id,
    ]);

    return $entity;
  }

  /**
   * Gets the name of the selected media bundle's source field.
   *
   * @return string
   *   The source field name.
   */
  protected function getSourceField() {
    $media_type = $this->entityTypeManager->getStorage('media_type')->load($this->getTargetBundle());
    $source_config = $media_type->getSource()->getConfiguration();

    return $source_config['source_field'];
  }

  /**
   * Gets uri of a directory to store file uploads in.
   *
   * @return string
   *   The uri of a directory for receiving uploads.
   */
  protected function getUploadLocation() {
    // Determine field mapping from media type.
    $media_type = $this->entityTypeManager->getStorage('media_type')->load($this->getTargetBundle());
    $source_field_config = $media_type->getSource()->getSourceFieldDefinition($media_type)->getSettings();

    $destination = trim($source_field_config['file_directory'], '/');

    // Replace tokens. As the tokens might contain HTML we convert it to plain
    // text.
    $destination = PlainTextOutput::renderFromHtml($this->token->replace($destination, []));
    $destination = $source_field_config['uri_scheme'] . '://' . $destination;

    if (!$this->fileSystem->prepareDirectory($destination, FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS)) {
      // @todo Error handling.
      return NULL;
    }

    return $destination;
  }

}
