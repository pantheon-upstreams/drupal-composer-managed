<?php

namespace Drupal\advagg\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * View AdvAgg information for this site.
 */
abstract class AdvaggFormBase extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /**
     * @var \Drupal\advagg\Form\AdvaggFormBase
     */
    $instance = parent::create($container);
    $instance->setRequestStack($container->get('request_stack'));

    return $instance;
  }

  /**
   * Checks if the form was submitted by AJAX.
   *
   * @return bool
   *   TRUE if the form was submitted via AJAX, otherwise FALSE.
   */
  protected function isAjax() {
    $request = $this->requestStack->getCurrentRequest();
    if ($request->query->has(FormBuilderInterface::AJAX_FORM_REQUEST)) {
      return TRUE;
    }
    return FALSE;
  }

}
