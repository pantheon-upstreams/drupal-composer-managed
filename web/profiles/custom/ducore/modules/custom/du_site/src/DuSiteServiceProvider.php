<?php

namespace Drupal\du_site;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceModifierInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * DU Site Service Provider.
 */
class DuSiteServiceProvider implements ServiceModifierInterface {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $definition = $container->getDefinition('entity.autocomplete_matcher');
    $definition->setClass('Drupal\du_site\EntityAutocompleteMatcher');
    $definition->setArguments([
      new Reference('plugin.manager.entity_reference_selection'),
      new Reference('date.formatter'),
      new Reference('entity_type.manager'),
    ]);
  }

}
