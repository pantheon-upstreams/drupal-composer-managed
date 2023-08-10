<?php

namespace Drupal\schema_metatag\Plugin\metatag\Tag;

use Drupal\Component\Render\PlainTextOutput;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\metatag\Plugin\metatag\Tag\MetaNameBase;
use Drupal\schema_metatag\Plugin\schema_metatag\PropertyTypeManager;
use Drupal\schema_metatag\SchemaMetatagManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * All Schema.org image tags should extend this class.
 */
class SchemaImageObjectBase extends SchemaNameBase {

  /**
   * {@inheritdoc}
   *
   * We don't want to render any output if there is no url.
   */
  public function output(): array {
    $result =  parent::output();
    if (empty($result['#attributes']['content']['url'])) {
      return [];
    }
    else {
      return $result;
    }
  }

}
