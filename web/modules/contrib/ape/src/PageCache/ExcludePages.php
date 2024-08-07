<?php

namespace Drupal\ape\PageCache;

use Drupal\Component\Plugin\Factory\FactoryInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\PageCache\ResponsePolicyInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cache policy that denies caching if the page matches a list of exclusions.
 *
 * This is in the response policy rather than request policy because a request
 * stack needs to be in place for the RequestPath plugin.
 */
class ExcludePages implements ResponsePolicyInterface {

  /**
   * A config object for the system performance configuration.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * Condition plugin manager.
   *
   * @var \Drupal\Component\Plugin\Factory\FactoryInterface $plugin_factory
   *   Factory for condition plugin manager.
   */
  protected $conditionManager;

  public function __construct(ConfigFactoryInterface $config_factory, FactoryInterface $plugin_factory) {
    $this->config = $config_factory->get('ape.settings');
    $this->conditionManager = $plugin_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function check(Response $response, Request $request) {
    /* @var \Drupal\system\Plugin\Condition\RequestPath $condition */
    $condition = $this->conditionManager->createInstance('request_path');
    $condition->setConfig('pages', $this->config->get('exclusions'));

    if (!empty($this->config->get('exclusions')) && $condition->evaluate()) {
      return static::DENY;
    }
  }

}
