<?php

namespace Drupal\du_site\Plugin\simple_sitemap\SitemapGenerator;

use Drupal\simple_sitemap\Plugin\simple_sitemap\SitemapGenerator\SitemapGeneratorBase;
use Drupal\Core\Config\FileStorage;

/**
 * Class Sitemap.
 *
 * @package Drupal\du_site\Plugin\simple_sitemap\SitemapGenerator
 *
 * @SitemapGenerator(
 *   id = "du_sitemap",
 *   label = @Translation("Sitemap index"),
 *   description = @Translation("Generates a sitemap index of all sitemaps."),
 * )
 */
class Sitemap extends SitemapGeneratorBase {

  /**
   * Generates and returns a sitemap chunk.
   *
   * @param array $links
   *   All links with their multilingual versions and settings.
   *
   * @return string
   *   Sitemap chunk.
   */
  protected function getXml(array $links) {
    $this->writer->openMemory();
    $this->writer->setIndent(TRUE);
    $this->writer->startSitemapDocument();
    $this->writer->writeGeneratedBy();
    $this->writer->startElement('sitemapindex');

    // Add attributes to document.
    $attributes = self::$indexAttributes;
    foreach ($attributes as $name => $value) {
      $this->writer->writeAttribute($name, $value);
    }

    // Add all of the variant links.
    foreach ($links as $link) {
      // The default sitemap should exist at the root of the domain instead of
      // under /default.
      $link['url'] = str_replace('default/', '', $link['url']);
      $this->writer->startElement('sitemap');
      $this->writer->writeElement('loc', $link['url']);
      $this->writer->endElement();
    }

    // Add all multisites by checking the list of sites from the content master
    // config.
    $config = new FileStorage('../config/lmucqz341');
    if ($config) {
      $sites = $config->read('du_central_management.settings');
      if (!empty($sites['sites'])) {
        foreach ($sites['sites'] as $site) {
          // Only include sites that want to be included in the sitemap.
          if (!empty($site['sitemap'])) {
            $this->writer->startElement('sitemap');
            $this->writer->writeElement('loc', 'https://' . $site['url'] . '/sitemap.xml');
            $this->writer->endElement();
          }
        }
      }
    }

    $this->writer->endElement();
    $this->writer->endDocument();

    return $this->writer->outputMemory();
  }

}
