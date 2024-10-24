<?php

namespace Drupal\du_widen;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceModifierInterface;

/**
 * DU Widen Service Provider.
 */
class DuWidenServiceProvider implements ServiceModifierInterface {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    if ($container->has('twig_tweak.twig_extension')) {
      $definition = $container->getDefinition('twig_tweak.twig_extension');
      $definition->setClass('Drupal\du_widen\TwigExtension');
    }
  }

}
