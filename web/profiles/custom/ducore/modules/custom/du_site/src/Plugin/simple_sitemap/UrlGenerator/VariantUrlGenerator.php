<?php

namespace Drupal\du_site\Plugin\simple_sitemap\UrlGenerator;

use Drupal\simple_sitemap\Plugin\simple_sitemap\UrlGenerator\EntityUrlGeneratorBase;
use Drupal\Core\Url;
use Drupal\simple_sitemap\EntityHelper;
use Drupal\simple_sitemap\Logger;
use Drupal\simple_sitemap\Simplesitemap;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Path\PathValidator;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class VariantUrlGenerator.
 *
 * @package Drupal\du_site\Plugin\simple_sitemap\UrlGenerator
 *
 * @UrlGenerator(
 *   id = "du_variant_url_generator",
 *   label = @Translation("DU Variant URL generator"),
 *   description = @Translation("Generates URLs for variants."),
 * )
 */
class VariantUrlGenerator extends EntityUrlGeneratorBase {

  /**
   * Path validator.
   *
   * @var \Drupal\Core\Path\PathValidator
   */
  protected $pathValidator;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    Simplesitemap $generator,
    Logger $logger,
    LanguageManagerInterface $language_manager,
    EntityTypeManagerInterface $entity_type_manager,
    EntityHelper $entityHelper,
    PathValidator $path_validator) {
    parent::__construct(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $generator,
      $logger,
      $language_manager,
      $entity_type_manager,
      $entityHelper
    );
    $this->pathValidator = $path_validator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('simple_sitemap.generator'),
      $container->get('simple_sitemap.logger'),
      $container->get('language_manager'),
      $container->get('entity_type.manager'),
      $container->get('simple_sitemap.entity_helper'),
      $container->get('path.validator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDataSets() {
    $variants = $this->generator->getSitemapManager()->getSitemapVariants();
    foreach ($variants as $key => &$variant) {
      $variant['path'] = '/sitemaps/' . $key . '/sitemap.xml';

      // Don't include sitemap of sitemaps.
      if ($variant['type'] == 'du_sitemap_of_sitemaps') {
        unset($variants[$key]);
      }
    }
    return $variants;
  }

  /**
   * {@inheritdoc}
   */
  protected function processDataSet($data_set) {
    if (!(bool) $this->pathValidator->getUrlIfValidWithoutAccessCheck($data_set['path'])) {
      $this->logger->m(self::PATH_DOES_NOT_EXIST_MESSAGE,
        ['@path' => $data_set['path'], '@custom_paths_url' => $GLOBALS['base_url'] . '/admin/config/search/simplesitemap/custom'])
        ->display('warning', 'administer sitemap settings')
        ->log('warning');
      return FALSE;
    }

    $url_object = Url::fromUserInput($data_set['path'], ['absolute' => TRUE]);
    $path = $url_object->getInternalPath();

    $path_data = [
      'url' => $url_object,
      'lastmod' => NULL,
      'priority' => isset($data_set['priority']) ? $data_set['priority'] : NULL,
      'changefreq' => !empty($data_set['changefreq']) ? $data_set['changefreq'] : NULL,
      'images' => [],
      'meta' => [
        'path' => $path,
      ],
    ];

    return $path_data;
  }

}
