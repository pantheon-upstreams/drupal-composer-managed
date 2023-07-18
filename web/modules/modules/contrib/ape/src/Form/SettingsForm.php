<?php

namespace Drupal\ape\Form;

use Drupal\Component\Plugin\Factory\FactoryInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SettingsForm extends ConfigFormBase {

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * Alternatives Condition
   *
   * @var \Drupal\system\Plugin\Condition\RequestPath
   */
  protected $alternatives;

  /**
   * Excluded Condition
   *
   * @var \Drupal\system\Plugin\Condition\RequestPath
   */
  protected $excluded;

  /**
   * Constructs a PerformanceForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   * @param \Drupal\Component\Plugin\Factory\FactoryInterface $plugin_factory
   *   Factory for condition plugin manager.
   */
  public function __construct(ConfigFactoryInterface $config_factory, DateFormatterInterface $date_formatter, FactoryInterface $plugin_factory) {
    parent::__construct($config_factory);

    $this->dateFormatter = $date_formatter;
    $this->excluded = $plugin_factory->createInstance('request_path');
    $this->alternatives = $plugin_factory->createInstance('request_path');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('date.formatter'),
      $container->get('plugin.manager.condition')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ape_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'ape.settings',
      'system.performance',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['#tree'] = TRUE;

    $config_system = $this->config('system.performance');
    $config_ape = $this->config('ape.settings');

    $form['page_caching'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('General page caching'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    );

    $period = [0, 60, 180, 300, 600, 900, 1800, 2700, 3600, 10800, 21600, 32400, 43200, 86400, 604800, 2592000, 31536000];
    $period = array_map(array($this->dateFormatter, 'formatInterval'), array_combine($period, $period));
    $period[0] = '<' . $this->t('no caching') . '>';

    $form['page_caching']['page_cache_maximum_age'] = array(
      '#type' => 'select',
      '#title' => $this->t('Global page expiration'),
      '#options' => $period,
      '#default_value' => $config_system->get('cache.page.max_age'),
      '#description' => $this->t('The standard expiration lifetime for cached pages. Ideally this is set as long as possible.'),
    );

    // Pages visibility plugin.
    $this->excluded->setConfig('pages', $config_ape->get('exclusions'));
    $form['page_caching'] += $this->excluded->buildConfigurationForm([], $form_state);
    unset($form['page_caching']['negate']);
    $form['page_caching']['pages']['#title'] = $this->t('Pages to exclude from caching');

    $form['page_caching_alternative'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Alternative page caching'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    );
    $form['page_caching_alternative']['ape_alternative_lifetime'] = array(
      '#type' => 'select',
      '#title' => $this->t('Alternative page expiration'),
      '#options' => $period,
      '#default_value' => $config_ape->get('lifetime.alternatives'),
      '#description' => $this->t('An alternative page expiration lifetime. Useful for pages that should refresh at a different rate than most pages, such as a short interval like 5 minutes.'),
    );

    // Pages visibility plugin.
    $this->alternatives->setConfig('pages', $config_ape->get('alternatives'));
    $form['page_caching_alternative'] += $this->alternatives->buildConfigurationForm([], $form_state);
    unset($form['page_caching_alternative']['negate']);
    $form['page_caching_alternative']['pages']['#title'] = $this->t('Pages that should apply alternative cache length');

    $form['server_codes'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Server response caching'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    );
    $form['server_codes']['ape_301_lifetime'] = array(
      '#type' => 'select',
      '#title' => $this->t('301 Redirects Expiration'),
      '#options' => $period,
      '#default_value' => $config_ape->get('lifetime.301'),
      '#description' => $this->t('Set a cache lifetime for all 301 redirects.'),
    );
    $form['server_codes']['ape_302_lifetime'] = array(
      '#type' => 'select',
      '#title' => $this->t('302 Redirects Expiration'),
      '#options' => $period,
      '#default_value' => $config_ape->get('lifetime.302'),
      '#description' => $this->t('Set a cache lifetime for all 302 redirects.'),
    );
    $form['server_codes']['ape_404_lifetime'] = array(
      '#type' => 'select',
      '#title' => $this->t('404 Page Not Found Expiration'),
      '#options' => $period,
      '#default_value' => $config_ape->get('lifetime.404'),
      '#description' => $this->t('Set a cache lifetime for all 404 Page Not Found responses.'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->excluded->setConfig('pages', $form_state->getValue(['page_caching', 'pages']));
    $this->alternatives->setConfig('pages', $form_state->getValue(['page_caching_alternative', 'pages']));

    $this->config('system.performance')
      ->set('cache.page.max_age', $form_state->getValue(['page_caching', 'page_cache_maximum_age']))
      ->save();

    $this->config('ape.settings')
      ->set('alternatives', $this->alternatives->getConfig()['pages'])
      ->set('exclusions', $this->excluded->getConfig()['pages'])
      ->set('lifetime.alternatives', $form_state->getValue(['page_caching_alternative', 'ape_alternative_lifetime']))
      ->set('lifetime.301', $form_state->getValue(['server_codes', 'ape_301_lifetime']))
      ->set('lifetime.302', $form_state->getValue(['server_codes', 'ape_302_lifetime']))
      ->set('lifetime.404', $form_state->getValue(['server_codes', 'ape_404_lifetime']))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
