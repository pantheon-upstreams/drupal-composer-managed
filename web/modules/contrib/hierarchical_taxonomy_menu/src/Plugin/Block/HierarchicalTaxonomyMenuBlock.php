<?php

namespace Drupal\hierarchical_taxonomy_menu\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\ResettableStackedRouteMatchInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'HierarchicalTaxonomyMenuBlock' block.
 *
 * @Block(
 *  id = "hierarchical_taxonomy_menu",
 *  admin_label = @Translation("Hierarchical Taxonomy Menu"),
 *  category = @Translation("Menus")
 * )
 */
class HierarchicalTaxonomyMenuBlock extends BlockBase implements ContainerFactoryPluginInterface {

  const SHOW_COUNT_NONE = '0';
  const SHOW_COUNT_NODE = '1';
  const SHOW_COUNT_COMMERCE_PRODUCT = '2';

  /**
   * Entity mapping.
   *
   * @var string[]
   */
  protected $entitiesMap = [
    self::SHOW_COUNT_NONE => '0',
    self::SHOW_COUNT_NODE => 'node',
    self::SHOW_COUNT_COMMERCE_PRODUCT => 'commerce_product',
  ];

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManager
   */
  protected $entityFieldManager;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The current route match service.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;

  /**
   * The the current primary database.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The entity type bundle info.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $entityTypeBundleInfo;

  /**
   * An array to hold the terms cache.
   *
   * @var array
   */
  protected static $terms = [];

  /**
   * Constructs a HierarchicalTaxonomyMenuBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The entity field manager service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager service.
   * @param \Drupal\Core\Routing\ResettableStackedRouteMatchInterface $current_route_match
   *   The current route match service.
   * @param \Drupal\Core\Database\Connection $database
   *   The the current primary database.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle info.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityFieldManagerInterface $entity_field_manager,
    EntityTypeManagerInterface $entity_type_manager,
    LanguageManagerInterface $language_manager,
    ResettableStackedRouteMatchInterface $current_route_match,
    Connection $database,
    EntityTypeBundleInfoInterface $entity_type_bundle_info
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityFieldManager = $entity_field_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->languageManager = $language_manager;
    $this->currentRouteMatch = $current_route_match;
    $this->database = $database;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_field.manager'),
      $container->get('entity_type.manager'),
      $container->get('language_manager'),
      $container->get('current_route_match'),
      $container->get('database'),
      $container->get('entity_type.bundle.info')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'vocabulary' => '',
      'max_depth' => 100,
      'dynamic_block_title' => FALSE,
      'collapsible' => FALSE,
      'stay_open' => FALSE,
      'interactive_parent' => FALSE,
      'hide_block' => FALSE,
      'use_image_style' => FALSE,
      'image_height' => 16,
      'image_width' => 16,
      'image_style' => '',
      'max_age' => 0,
      'base_term' => '',
      'dynamic_base_term' => FALSE,
      'show_count' => '0',
      'referencing_field' => '_none',
      'calculate_count_recursively' => FALSE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['basic'] = [
      '#type' => 'details',
      '#title' => $this->t('Basic settings'),
    ];

    $form['basic']['vocabulary'] = [
      '#title' => $this->t('Use taxonomy terms from this vocabulary to create a menu'),
      '#type' => 'select',
      '#options' => $this->getVocabularyOptions(),
      '#required' => TRUE,
      '#default_value' => $this->configuration['vocabulary'],
      '#description' => $this->t('You can display an image next to a menu item if your vocabulary has an image field.'),
    ];

    $form['basic']['max_depth'] = [
      '#title' => $this->t('Number of sublevels to display'),
      '#type' => 'select',
      '#options' => [
        '0' => '0',
        '1' => '1',
        '2' => '2',
        '3' => '3',
        '4' => '4',
        '5' => '5',
        '6' => '6',
        '7' => '7',
        '8' => '8',
        '9' => '9',
        '10' => '10',
        '100' => $this->t('Unlimited'),
      ],
      '#default_value' => $this->configuration['max_depth'],
    ];

    $form['basic']['dynamic_block_title'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Make the block title match the current taxonomy term name'),
      '#default_value' => $this->configuration['dynamic_block_title'],
    ];

    $form['basic']['collapsible'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Make the menu collapsed by default'),
      '#default_value' => $this->configuration['collapsible'],
    ];

    $form['basic']['stay_open'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Stay open at the current taxonomy term'),
      '#default_value' => $this->configuration['stay_open'],
      '#states' => [
        'visible' => [
          [
            ':input[name="settings[basic][collapsible]"]' => ['checked' => TRUE],
          ],
        ],
      ],
    ];

    $form['basic']['interactive_parent'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Allow parent items to be collapsible and selectable'),
      '#default_value' => $this->configuration['interactive_parent'],
      '#states' => [
        'visible' => [
          [
            ':input[name="settings[basic][collapsible]"]' => ['checked' => TRUE],
          ],
        ],
      ],
    ];

    $form['basic']['hide_block'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Hide block if the output is empty'),
      '#default_value' => $this->configuration['hide_block'],
    ];

    $form['image'] = [
      '#type' => 'details',
      '#title' => $this->t('Image settings'),
    ];

    $form['image']['notice'] = [
      '#type' => 'markup',
      '#markup' => $this->t('If you are displaying an image next to menu items you can choose the size for that image. You can do that by providing the image size in pixels or by using an image style.'),
    ];

    $form['image']['use_image_style'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use image style'),
      '#default_value' => $this->configuration['use_image_style'],
    ];

    $form['image']['image_height'] = [
      '#type' => 'number',
      '#title' => $this->t('Image height (px)'),
      '#default_value' => $this->configuration['image_height'],
      '#states' => [
        'visible' => [
          [
            ':input[name="settings[image][use_image_style]"]' => ['checked' => FALSE],
          ],
        ],
      ],
    ];

    $form['image']['image_width'] = [
      '#type' => 'number',
      '#title' => $this->t('Image width (px)'),
      '#default_value' => $this->configuration['image_width'],
      '#states' => [
        'visible' => [
          [
            ':input[name="settings[image][use_image_style]"]' => ['checked' => FALSE],
          ],
        ],
      ],
    ];

    $form['image']['image_style'] = [
      '#title' => $this->t('Image style'),
      '#type' => 'select',
      '#options' => $this->getImageStyleOptions(),
      '#default_value' => $this->configuration['image_style'],
      '#states' => [
        'visible' => [
          [
            ':input[name="settings[image][use_image_style]"]' => ['checked' => TRUE],
          ],
        ],
      ],
    ];

    $form['advanced'] = [
      '#type' => 'details',
      '#title' => $this->t('Advanced settings'),
    ];

    $form['advanced']['max_age'] = [
      '#title' => $this->t('Cache'),
      '#type' => 'select',
      '#options' => [
        '0' => $this->t('No Caching'),
        '1800' => $this->t('30 Minutes'),
        '3600' => $this->t('1 Hour'),
        '21600' => $this->t('6 Hours'),
        '43200' => $this->t('12 Hours'),
        '86400' => $this->t('1 Day'),
        '604800' => $this->t('1 Week'),
        'PERMANENT' => $this->t('Permanent'),
      ],
      '#default_value' => $this->configuration['max_age'],
      '#description' => $this->t('Set the max age the menu is allowed to be cached for.'),
    ];

    $form['advanced']['base_term'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Base term'),
      '#size' => 20,
      '#default_value' => $this->configuration['base_term'],
      '#description' => $this->t('Enter a base term and menu items will only be generated for its children. You can enter term ID or term name. Leave empty to generate menu for the entire vocabulary.'),
      '#states' => [
        'visible' => [
          ':input[name="settings[advanced][dynamic_base_term]"]' => ['checked' => FALSE],
        ],
      ],
    ];

    $form['advanced']['dynamic_base_term'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Dynamic Base term'),
      '#default_value' => $this->configuration['dynamic_base_term'],
      '#description' => $this->t('Automatically set the base term from taxonomy page. The base term is then set to the current term and menu items will only be generated for its children.'),
    ];

    $form['advanced']['show_count'] = [
      '#type' => 'radios',
      '#title' => $this->t('Show count of referencing entities'),
      '#options' => [
        0 => $this->t('No'),
        1 => $this->t('Show count of referencing nodes'),
        2 => $this->t('Show count of referencing commerce products'),
      ],
      '#default_value' => $this->configuration['show_count'],
    ];

    $form['advanced']['referencing_field'] = [
      '#type' => 'select',
      '#title' => $this->t('Referencing field'),
      '#options' => $this->getReferencingFields(),
      '#default_value' => $this->configuration['referencing_field'],
      '#states' => [
        'visible' => [
          ':input[name="settings[advanced][show_count]"]' => ['value' => '2'],
        ],
      ],
    ];

    $form['advanced']['calculate_count_recursively'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Calculate count recursively'),
      '#default_value' => $this->configuration['calculate_count_recursively'],
    ];

    return $form;
  }

  /**
   * Generates vocabulary select options.
   */
  protected function getVocabularyOptions() {
    $options = [];
    $vocabularies = taxonomy_vocabulary_get_names();

    foreach ($vocabularies as $vocabulary) {
      $fields = $this->entityFieldManager->getFieldDefinitions('taxonomy_term', $vocabulary);
      $options[$vocabulary . '|'] = $this->t('@vocabulary (no image)', ['@vocabulary' => ucfirst($vocabulary)]);

      foreach ($fields as $field) {
        if ($field->getType() == 'image' || $this->isMediaImage($field)) {
          $field_name = $field->getName();
          $options[$vocabulary . '|' . $field_name] = $this->t('@vocabulary (with image: @image_field)', [
            '@vocabulary' => ucfirst($vocabulary),
            '@image_field' => $field_name,
          ]);
        }
      }

    }

    return $options;
  }

  /**
   * Whether a field is media type of image.
   *
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field
   *   A field to check.
   *
   * @return bool
   *   TRUE if this field is media type of image.
   */
  protected function isMediaImage(FieldDefinitionInterface $field) {
    if ($field->getType() == 'entity_reference' && $field->getSetting('target_type') == 'media') {
      if (isset($field->getSetting('handler_settings')['target_bundles']['image'])) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function blockValidate($form, FormStateInterface $form_state) {
    if (
      $form_state->getValue(
        [
          'advanced',
          'show_count',
        ]
      ) == self::SHOW_COUNT_COMMERCE_PRODUCT &&
      $form_state->getValue(
        [
          'advanced',
          'referencing_field',
        ]
      ) == '_none'
    ) {
      $form_state->setErrorByName('advanced][referencing_field', $this->t('Please select referencing field'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['vocabulary'] = $form_state->getValue(
      [
        'basic',
        'vocabulary',
      ]
    );
    $this->configuration['max_depth'] = $form_state->getValue(
      [
        'basic',
        'max_depth',
      ]
    );
    $this->configuration['dynamic_block_title'] = $form_state->getValue(
      [
        'basic',
        'dynamic_block_title',
      ]
    );
    $this->configuration['collapsible'] = $form_state->getValue(
      [
        'basic',
        'collapsible',
      ]
    );
    $this->configuration['stay_open'] = $form_state->getValue(
      [
        'basic',
        'stay_open',
      ]
    );
    $this->configuration['interactive_parent'] = $form_state->getValue(
      [
        'basic',
        'interactive_parent',
      ]
    );
    $this->configuration['hide_block'] = $form_state->getValue(
      [
        'basic',
        'hide_block',
      ]
    );
    $this->configuration['use_image_style'] = $form_state->getValue(
      [
        'image',
        'use_image_style',
      ]
    );
    $this->configuration['image_height'] = $form_state->getValue(
      [
        'image',
        'image_height',
      ]
    );
    $this->configuration['image_width'] = $form_state->getValue(
      [
        'image',
        'image_width',
      ]
    );
    $this->configuration['image_style'] = $form_state->getValue(
      [
        'image',
        'image_style',
      ]
    );
    $this->configuration['max_age'] = $form_state->getValue(
      [
        'advanced',
        'max_age',
      ]
    );
    $this->configuration['base_term'] = $form_state->getValue(
      [
        'advanced',
        'base_term',
      ]
    );
    $this->configuration['dynamic_base_term'] = $form_state->getValue(
      [
        'advanced',
        'dynamic_base_term',
      ]
    );
    $this->configuration['show_count'] = $form_state->getValue(
      [
        'advanced',
        'show_count',
      ]
    );
    $this->configuration['referencing_field'] = $form_state->getValue(
      [
        'advanced',
        'referencing_field',
      ]
    );
    $this->configuration['calculate_count_recursively'] = $form_state->getValue(
      [
        'advanced',
        'calculate_count_recursively',
      ]
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $vocabulary_config = $this->configuration['vocabulary'];
    $vocabulary_config = explode('|', $vocabulary_config);
    $vocabulary = isset($vocabulary_config[0]) ? $vocabulary_config[0] : NULL;
    $base_term = $this->getVocabularyBaseTerm($this->configuration['base_term'], $this->configuration['dynamic_base_term']);
    $max_depth = $this->configuration['max_depth'];
    $vocabulary_tree = $this->entityTypeManager->getStorage('taxonomy_term')
      ->loadTree($vocabulary, $base_term, $max_depth + 1);

    if ($this->configuration['hide_block'] && !$vocabulary_tree) {
      return [];
    }

    $image_field = isset($vocabulary_config[1]) ? $vocabulary_config[1] : NULL;
    $use_image_style = $this->configuration['use_image_style'];
    $image_height = $this->configuration['image_height'];
    $image_width = $this->configuration['image_width'];
    $image_style = $use_image_style == TRUE ? $this->configuration['image_style'] : NULL;
    $route_tid = $this->getCurrentRoute();
    $max_age = $this->getMaxAge($this->configuration['max_age']);
    $interactive_parent = $this->configuration['collapsible'] ? $this->configuration['interactive_parent'] : 0;
    $show_count = $this->configuration['show_count'];
    $referencing_field = $this->configuration['referencing_field'];

    $vocabulary_tree_array = [];

    foreach ($vocabulary_tree as $item) {
      $vocabulary_tree_array[] = [
        'tid' => $item->tid,
        'status' => $this->getStatusFromTid($item->tid),
        'name' => $this->getNameFromTid($item->tid),
        'url' => $this->getLinkFromTid($item->tid),
        'parents' => $item->parents,
        'use_image_style' => $use_image_style,
        'image' => $this->getImageFromTid($item->tid, $image_field, $image_style),
        'height' => $image_height != '' ? $image_height : 16,
        'width' => $image_width != '' ? $image_width : 16,
        'interactive_parent' => $interactive_parent,
        'show_count' => $show_count,
        'entities' => !empty($show_count) ? $this->getEntityIds($this->entitiesMap[$show_count], $referencing_field, $item->tid, $vocabulary, $this->configuration['calculate_count_recursively']) : [],
      ];
    }

    $tree = $this->generateTree($vocabulary_tree_array, $base_term);

    return [
      '#theme' => 'hierarchical_taxonomy_menu',
      '#menu_tree' => $tree,
      '#route_tid' => $route_tid,
      '#cache' => [
        'max-age' => $max_age,
        'tags' => [
          'taxonomy_term_list',
        ],
      ],
      '#current_depth' => 0,
      '#vocabulary' => $vocabulary,
      '#max_depth' => $max_depth,
      '#collapsible' => $this->configuration['collapsible'],
      '#attached' => [
        'library' => [
          'hierarchical_taxonomy_menu/hierarchical_taxonomy_menu',
        ],
        'drupalSettings' => [
          'stayOpen' => $this->configuration['stay_open'],
          'interactiveParentMenu' => $this->configuration['interactive_parent'],
        ],
      ],
    ];
  }

  /**
   * Generates menu tree.
   */
  protected function generateTree($array, $parent = 0) {
    $tree = [];

    foreach ($array as $item) {
      if (reset($item['parents']) == $parent && $item['status'] == 1) {
        $item['subitem'] = isset($item['subitem']) ? $item['subitem'] : $this->generateTree($array, $item['tid']);
        $tree[] = $item;
      }
    }

    return $tree;
  }

  /**
   * Gets term name.
   */
  protected function getNameFromTid($tid) {
    $language = $this->languageManager->getCurrentLanguage()->getId();

    if (isset(self::$terms[$tid])) {
      $term = self::$terms[$tid];
    }
    else {
      $term = $this->entityTypeManager->getStorage('taxonomy_term')->load($tid);
      self::$terms[$tid] = $term;
    }

    $translation_languages = $term->getTranslationLanguages();

    if (isset($translation_languages[$language])) {
      $term_translated = $term->getTranslation($language);
      return $term_translated->getName();
    }

    return $term->getName();
  }

  /**
   * Gets term status.
   */
  protected function getStatusFromTid($tid) {
    $language = $this->languageManager->getCurrentLanguage()->getId();

    if (isset(self::$terms[$tid])) {
      $term = self::$terms[$tid];
    }
    else {
      $term = $this->entityTypeManager->getStorage('taxonomy_term')->load($tid);
      self::$terms[$tid] = $term;
    }

    $translation_languages = $term->getTranslationLanguages();

    if (isset($translation_languages[$language])) {
      $term_translated = $term->getTranslation($language);
      return $term_translated->status->value;
    }

    return $term->status->value;
  }

  /**
   * Gets term url.
   */
  protected function getLinkFromTid($tid) {
    $language = $this->languageManager->getCurrentLanguage()->getId();

    if (isset(self::$terms[$tid])) {
      $term = self::$terms[$tid];
    }
    else {
      $term = $this->entityTypeManager->getStorage('taxonomy_term')->load($tid);
      self::$terms[$tid] = $term;
    }

    $translation_languages = $term->getTranslationLanguages();

    if (isset($translation_languages[$language])) {
      /** @var \Drupal\taxonomy\TermInterface $term_translated */
      $term_translated = $term->getTranslation($language);
      return $term_translated->toUrl();
    }

    return $term->toUrl();
  }

  /**
   * Gets current route.
   */
  protected function getCurrentRoute() {
    if ($term_id = $this->currentRouteMatch->getRawParameter('taxonomy_term')) {
      return $term_id;
    }

    return NULL;
  }

  /**
   * Gets image from term.
   */
  protected function getImageFromTid($tid, $image_field, $image_style) {
    if (!is_numeric($tid) || $image_field == '') {
      return '';
    }

    if (isset(self::$terms[$tid])) {
      $term = self::$terms[$tid];
    }
    else {
      $term = $this->entityTypeManager->getStorage('taxonomy_term')->load($tid);
      self::$terms[$tid] = $term;
    }

    $image_field_name = $term->get($image_field)->getValue();
    $image_field_type = $term->get($image_field)->getFieldDefinition()->getType();

    if (!isset($image_field_name[0]['target_id'])) {
      return '';
    }

    if ($image_field_type == 'image') {
      $fid = $image_field_name[0]['target_id'];
    }
    else {
      // A field of media type.
      $fid = FALSE;
      foreach ($image_field_name as $value) {
        $media = $value['target_id'];
        $media = $this->entityTypeManager->getStorage('media')->load($media);
        if ($media && $media->bundle() == 'image') {
          foreach ($media->referencedEntities() as $item) {
            if ($item->getEntityTypeId() == 'file') {
              $fid = $item->id();
              break;
            }
          }
        }
        if ($fid) {
          break;
        }
      }
    }

    if ($fid) {
      $file = $this->entityTypeManager->getStorage('file')->load($fid);
      if ($image_style) {
        $style = $this->entityTypeManager->getStorage('image_style')->load($image_style);
        if ($style) {
          $path = $style->buildUrl($file->getFileUri());
        }
        else {
          $path = Url::fromUri(file_create_url($file->getFileUri()));
        }
      }
      else {
        $path = Url::fromUri(file_create_url($file->getFileUri()));
      }
      return $path;
    }

    return '';
  }

  /**
   * Generates image style select options.
   */
  protected function getImageStyleOptions() {
    $options = [];
    $styles = $this->entityTypeManager->getStorage('image_style')->loadMultiple();

    foreach ($styles as $style) {
      /** @var \Drupal\image\Entity\ImageStyle $style */
      $style_name = $style->getName();
      $options[$style_name] = $style->label();
    }

    return $options;
  }

  /**
   * Returns base taxonomy term ID.
   */
  protected function getVocabularyBaseTerm($base_term, $dynamic_base_term) {
    if ($dynamic_base_term) {
      if ($term_id = $this->currentRouteMatch->getRawParameter('taxonomy_term')) {
        return $term_id;
      }
      else {
        return 0;
      }
    }
    else {
      if (!$base_term) {
        return 0;
      }
      if (is_numeric($base_term)) {
        return $base_term;
      }
      else {
        $term = $this->entityTypeManager->getStorage('taxonomy_term')
          ->loadByProperties(['name' => $base_term]);
        return $term ? reset($term)->id() : 0;
      }
    }

  }

  /**
   * Returns Cache Max Age.
   */
  public function getMaxAge($max_age) {
    if (!$max_age) {
      $max_age = 0;
      return $max_age;
    }

    if ($max_age == 'PERMANENT') {
      $max_age = Cache::PERMANENT;
      return $max_age;
    }
    else {
      return $max_age;
    }
  }

  /**
   * Gets all entities referencing the given term.
   */
  protected function getEntityIds($entity_type_id, $field_name, $tid, $vocabulary, $calculate_count_recursively) {
    if (!$calculate_count_recursively) {
      return $this->getEntityIdsForTerm($entity_type_id, $field_name, $tid);
    }
    else {
      $entity_ids = $this->getEntityIdsForTerm($entity_type_id, $field_name, $tid);

      $child_tids = $this->entityTypeManager
        ->getStorage('taxonomy_term')
        ->loadTree($vocabulary, $tid);

      foreach ($child_tids as $child_tid) {
        $entity_ids = array_merge($entity_ids, $this->getEntityIdsForTerm($entity_type_id, $field_name, $child_tid->tid));
      }

      return $entity_ids;
    }
  }

  /**
   * Gets entities referencing the given term.
   */
  protected function getEntityIdsForTerm($entity_type_id, $field_name, $tid) {
    if (empty($field_name)) {
      return [];
    }

    if ($entity_type_id == 'node') {
      return $this->database->select('taxonomy_index', 'ta')
        ->fields('ta', ['nid'])->distinct(TRUE)
        ->condition('tid', $tid)
        ->execute()->fetchCol();
    }
    else {
      return $this->database->select('commerce_product__' . $field_name, 'cp')
        ->fields('cp', ['entity_id'])->distinct(TRUE)
        ->condition($field_name . '_target_id', $tid)
        ->execute()->fetchCol();
    }
  }

  /**
   * Gets taxonomy term fields from commerce product entity.
   *
   * @return array
   *   An array of taxonomy term fields.
   */
  protected function getReferencingFields() {
    $referencing_fields = [];
    $referencing_fields['_none'] = $this->t('- None -');

    $bundles = $this->entityTypeBundleInfo
      ->getBundleInfo('commerce_product');

    foreach ($bundles as $bundle => $data) {
      $fields = $this->entityFieldManager
        ->getFieldDefinitions('commerce_product', $bundle);

      /** @var \Drupal\Core\Field\FieldDefinitionInterface $field */
      foreach ($fields as $field) {
        if ($field->getType() == 'entity_reference' && $field->getSetting('target_type') == 'taxonomy_term') {
          $referencing_fields[$field->getName()] = $field->getLabel();
        }
      }
    }

    return $referencing_fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    $dynamic_base_term = $this->configuration['dynamic_base_term'];
    if ($dynamic_base_term) {
      $base_term = $this->getVocabularyBaseTerm($this->configuration['base_term'], $dynamic_base_term);
      if (!$base_term) {
        return parent::getCacheContexts();
      }
    }

    return Cache::mergeContexts(parent::getCacheContexts(), ['url.path']);
  }

}
