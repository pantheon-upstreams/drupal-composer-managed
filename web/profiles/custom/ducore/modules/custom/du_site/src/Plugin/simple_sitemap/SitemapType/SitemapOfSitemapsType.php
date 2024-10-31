<?php

namespace Drupal\du_site\Plugin\simple_sitemap\SitemapType;

use Drupal\simple_sitemap\Plugin\simple_sitemap\SitemapType\SitemapTypeBase;

/**
 * Class SitemapOfSitemapsType.
 *
 * @package Drupal\du_site\Plugin\simple_sitemap\SitemapType
 *
 * @SitemapType(
 *   id = "du_sitemap_of_sitemaps",
 *   label = @Translation("Sitemap of Sitemaps"),
 *   description = @Translation("The sitemap of sitemaps type."),
 *   sitemapGenerator = "du_sitemap",
 *   urlGenerators = {
 *     "du_variant_url_generator"
 *   },
 * )
 */
class SitemapOfSitemapsType extends SitemapTypeBase {
}
