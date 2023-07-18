<?php

namespace Drupal\advagg_old_ie_compatibility\Form;

use Drupal\advagg\AdvaggSettersTrait;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure advagg ie compatibility for this site.
 */
class SettingsForm extends ConfigFormBase {

  use AdvaggSettersTrait;

  /**
   * The Advagg cache.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cache;

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The core language manager service.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /**
     * @var \Drupal\advagg_old_ie_compatibility\Form\SettingsForm
     */
    $instance = parent::create($container);
    $instance->setCache($container->get('cache.advagg'));

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'advagg_old_ie_compatibility_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['advagg_old_ie_compatibility.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('advagg_old_ie_compatibility.settings');
    $form['active'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Prevent more than %limit CSS selectors in an aggregated CSS file', ['%limit' => $config->get('limit')]),
      '#default_value' => $config->get('active'),
      '#description' => $this->t('Internet Explorer before version 10; IE9, IE8, IE7, and IE6 all have 4095 as the limit for the maximum number of css selectors that can be in a file. Enabling this will prevent CSS aggregates from being created that exceed this limit. <a href="@link">More info</a>.', ['@link' => 'http://blogs.msdn.com/b/ieinternals/archive/2011/05/14/10164546.aspx']),
    ];
    $form['limit'] = [
      '#type' => 'textfield',
      '#title' => $this->t('The selector count the IE CSS limiter should use'),
      '#default_value' => $config->get('limit'),
      '#description' => $this->t('Internet Explorer before version 10; IE9, IE8, IE7, and IE6 all have 4095 as the limit for the maximum number of css selectors that can be in a file. Use this field to modify the value used.'),
      '#states' => [
        'visible' => [
          '#edit-active' => ['checked' => TRUE],
        ],
        'disabled' => [
          '#edit-active' => ['checked' => FALSE],
        ],
      ],
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\Core\Config\Config $config */
    $config = $this->config('advagg_old_ie_compatibility.settings');
    $config
      ->set('active', $form_state->getValue('active'))
      ->set('limit', $form_state->getValue('limit'))
      ->save();

    // Clear relevant caches.
    Cache::invalidateTags(['library_info']);
    $this->cache->invalidateAll();

    parent::submitForm($form, $form_state);
  }

}
