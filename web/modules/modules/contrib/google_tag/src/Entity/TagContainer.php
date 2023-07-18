<?php

namespace Drupal\google_tag\Entity;

use Drupal\Core\Condition\ConditionPluginCollection;
use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityWithPluginCollectionInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Defines the Google Tag configuration entity.
 *
 * @ConfigEntityType(
 *   id = "google_tag_container",
 *   label = @Translation("Google Tag Container"),
 *   label_singular = @Translation("container"),
 *   label_plural = @Translation("containers"),
 *   label_collection = @Translation("Google Tag containers"),
 *   handlers = {
 *     "list_builder" = "Drupal\google_tag\TagContainerListBuilder",
 *     "form" = {
 *       "default" = "Drupal\google_tag\Form\TagContainerForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     },
 *   },
 *   admin_permission = "administer google_tag_container",
 *   config_prefix = "container",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "status" = "status"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "status",
 *     "weight",
 *     "tag_container_ids",
 *     "advanced_settings",
 *     "dimensions_metrics",
 *     "conditions",
 *     "events",
 *   },
 *   links = {
 *     "add-form" = "/admin/config/services/google-tag/add",
 *     "edit-form" = "/admin/config/services/google-tag/manage/{google_tag_container}",
 *     "delete-form" = "/admin/config/services/google-tag/manage/{google_tag_container}/delete",
 *     "enable" = "/admin/config/services/google-tag/manage/{google_tag_container}/enable",
 *     "disable" = "/admin/config/services/google-tag/manage/{google_tag_container}/disable",
 *     "collection" = "/admin/config/services/google-tag/containers",
 *   }
 * )
 */
class TagContainer extends ConfigEntityBase implements EntityWithPluginCollectionInterface {

  use StringTranslationTrait;

  /**
   * Define the Acceptable Google Tag and GTM ID Patterns.
   */
  const GOOGLE_TAG_MATCH = '(?:GT|UA|G|AW|DC|GTM)-[0-9a-zA-Z]{5,}(?:-[0-9]{1,})?';

  /**
   * Define the Acceptable Measurement ID patterns.
   */
  const MEASUREMENT_ID_MATCH = '/(?:UA|G|GT|AW|DC)-[0-9a-zA-Z]{5,}(?:-[0-9]{1,})?/';

  /**
   * Define the pattern matching legacy universal analytics account.
   */
  const GOOGLE_ANALYTICS_UA_MATCH = '/(?:UA)-[0-9a-zA-Z]{5,}(?:-[0-9]{1,})?/';

  /**
   * Define the Acceptable Google Tag Manager Container IDs.
   */
  const GOOGLE_TAG_MANAGER_MATCH = '/(?:GTM)-[0-9a-zA-Z]{5,}/';

  /**
   * The machine name for the configuration entity.
   *
   * @var string
   */
  protected $id;

  /**
   * The human-readable name of the configuration entity.
   *
   * @var string
   */
  protected $label;

  /**
   * The weight of the configuration entity.
   *
   * @var int
   */
  protected $weight = 0;

  /**
   * The Google Tag measurement or container id(s).
   *
   * @var string[]
   */
  protected $tag_container_ids = [];

  /**
   * Advanced Settings.
   *
   * @var array
   */
  protected $advanced_settings = [];

  /**
   * Custom dimensions and metrics.
   *
   * @var array
   */
  protected array $dimensions_metrics = [];

  /**
   * The insertion conditions.
   *
   * Each item is the configuration array not the condition object.
   *
   * @var array
   */
  protected array $conditions = [];

  /**
   * Event plugin configuration.
   *
   * @var array
   */
  protected array $events = [];

  /**
   * The insertion condition collection.
   *
   * @var \Drupal\Core\Condition\ConditionPluginCollection|null
   */
  protected $conditionCollection;

  /**
   * The condition plugin manager.
   *
   * @var \Drupal\Core\Executable\ExecutableManagerInterface|null
   */
  protected $conditionPluginManager;

  /**
   * Return the Gtm id.
   *
   * @return string
   *   Gtm Id.
   */
  public function getGtmId(): string {
    return current($this->getGtmIds(1)) ?: '';
  }

  /**
   * Return the Gtm id.
   *
   * @return array
   *   Gtm Id.
   */
  public function getGtmIds($length = NULL): array {
    $default_tag = array_slice(
      array_filter(
        $this->tag_container_ids,
        static fn ($id) => preg_match(self::GOOGLE_TAG_MANAGER_MATCH, $id)
      ),
      0,
      $length
    );
    return $default_tag ?: [];
  }

  /**
   * Return GTM Advanced Settings.
   *
   * @return array
   *   Gtm settings.
   */
  public function getGtmSettings(): array {
    $advanced_settings = $this->get('advanced_settings');
    return $advanced_settings['gtm'] ?? [];
  }

  /**
   * Return the first tag as the default.
   *
   * @return string
   *   Default tag id.
   */
  public function getDefaultTagId(): string {
    $default_tag = array_slice(
      array_filter(
        $this->tag_container_ids,
        static fn ($id) => preg_match(self::GOOGLE_TAG_MANAGER_MATCH, $id) === 0),
      0,
      1
    );
    return current($default_tag) ?: '';
  }

  /**
   * Returns additional ids.
   *
   * @return array
   *   Additional ids.
   */
  public function getAdditionalIds(): array {
    return array_slice(
      array_filter(
        $this->tag_container_ids,
        static fn($id) => preg_match(self::GOOGLE_TAG_MANAGER_MATCH, $id) === 0),
      1
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginCollections() {
    return [
      'conditions' => $this->getInsertionConditions(),
    ];
  }

  /**
   * Checks whether event plugin is configured or not.
   *
   * @param string $plugin_id
   *   Event plugin id.
   *
   * @return bool
   *   True if configured else false.
   */
  public function hasEvent(string $plugin_id): bool {
    return array_key_exists($plugin_id, $this->events);
  }

  /**
   * Returns event plugin config if configured.
   *
   * @param string $plugin_id
   *   Event plugin id.
   *
   * @return array
   *   Event plugin config.
   */
  public function getEventConfiguration(string $plugin_id): array {
    return $this->events[$plugin_id] ?? [];
  }

  /**
   * Returns the set of insertion conditions for this tag container.
   *
   * @return \Drupal\Core\Condition\ConditionPluginCollection
   *   A collection of configured condition plugins.
   */
  public function getInsertionConditions() {
    if ($this->conditionCollection === NULL) {
      $this->conditionCollection = new ConditionPluginCollection($this->conditionPluginManager(), $this->get('conditions'));
    }
    return $this->conditionCollection;
  }

  /**
   * Gets the condition plugin manager.
   *
   * @return \Drupal\Core\Executable\ExecutableManagerInterface
   *   The condition plugin manager.
   */
  protected function conditionPluginManager() {
    if ($this->conditionPluginManager === NULL) {
      $this->conditionPluginManager = \Drupal::service('plugin.manager.condition');
    }
    return $this->conditionPluginManager;
  }

  /**
   * Returns custom dimension and metrics.
   *
   * @return array
   *   Dimension and metrics attribute.
   */
  public function getDimensionsAndMetrics(): array {
    return $this->dimensions_metrics;
  }

}
