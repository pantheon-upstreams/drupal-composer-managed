<?php

namespace Drupal\advagg_cdn\Form;

use Drupal\advagg\AdvaggSettersTrait;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure advagg_js_minify settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  use AdvaggSettersTrait;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /**
     * @var \Drupal\advagg_cdn\Form\SettingsForm
     */
    $instance = parent::create($container);
    $instance->setCssCollectionOptimizer($container->get('asset.css.collection_optimizer'));
    $instance->setJsCollectionOptimizer($container->get('asset.js.collection_optimizer'));

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'advagg_cdn_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['advagg_cdn.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('advagg_cdn.settings');
    $form = [];

    $form['cdn'] = [
      '#type' => 'radios',
      '#title' => $this->t('CDN to use'),
      '#default_value' => $config->get('cdn'),
      '#options' => ['google' => 'Google', 'microsoft' => 'Microsoft'],
    ];
    $form['minified'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use Minified Resources'),
      '#default_value' => $config->get('minified'),
      '#description' => $this->t('When available use minified versions of any resources being served by the CDN.'),
    ];
    if ($this->config('advagg.settings')->get('cache_level') < 0) {
      $form['minified']['#description'] .= $this->t('This setting will not have any effect because AdvAgg is currently in <a href="@devel">development mode</a>. Once the cache settings have been set to normal or aggressive, JS minification will take place.', ['@devel' => Url::fromRoute('advagg.settings', ['fragment' => 'edit-advagg-cache-level'])->toString()]);
    }

    $form['jquery'] = [
      '#type' => 'details',
      '#title' => 'jQuery',
    ];
    $form['jquery']['jquery_active'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Serve jQuery by CDN'),
      '#default_value' => $config->get('jquery'),
    ];
    $form['jquery']['jquery_version'] = [
      '#type' => 'textfield',
      '#title' => $this->t('jQuery version'),
      '#default_value' => $config->get('jquery_version'),
      '#description' => $this->t('Version of jQuery to load.'),
      '#states' => [
        'disabled' => [
          ':input[name="jquery_active"]' => ['value' => "0"],
        ],
      ],
    ];
    $form['jquery_ui'] = [
      '#type' => 'details',
      '#title' => 'jQuery UI',
    ];
    $form['jquery_ui']['jquery_ui_css'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Serve jQuery UI CSS by CDN'),
      '#default_value' => $config->get('jquery_ui_css'),
      '#description' => $this->t('Warning: this may change your site appearance as Drupal 8 by default uses the base jQuery theme and the base theme is not available by CDN.'),
    ];
    $jquery_themes = [
      'black-tie',
      'blitzer',
      'cupertino',
      'dark-hive',
      'dot-luv',
      'eggplant',
      'excite-bike',
      'flick',
      'hot-sneaks',
      'humanity',
      'le-frog',
      'mint-choc',
      'overcast',
      'pepper-grinder',
      'redmond',
      'smoothness',
      'south-street',
      'start',
      'sunny',
      'swanky-purse',
      'trontastic',
      'ui-darkness',
      'ui-lightness',
      'vader',
    ];
    $form['jquery_ui']['jquery_ui_theme'] = [
      '#type' => 'select',
      '#title' => $this->t('jQuery UI theme'),
      '#default_value' => $config->get('jquery_ui_theme'),
      '#description' => $this->t('Choose which jQuery theme to use. Smoothness is the most basic and similar to Drupal standard version.'),
      '#options' => array_combine($jquery_themes, $jquery_themes),
    ];
    $form['jquery_ui']['jquery_ui_js'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Serve jQuery UI JavaScript by CDN'),
      '#default_value' => $config->get('jquery_ui_js'),
    ];
    $form['jquery_ui']['jquery_ui_version'] = [
      '#type' => 'textfield',
      '#title' => $this->t('jQuery UI version'),
      '#default_value' => $config->get('jquery_ui_version'),
      '#description' => $this->t('Version of jQuery UI to load.'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('advagg_cdn.settings')
      ->set('cdn', $form_state->getValue('cdn'))
      ->set('jquery', $form_state->getValue('jquery_active'))
      ->set('jquery_version', $form_state->getValue('jquery_version'))
      ->set('jquery_ui_css', $form_state->getValue('jquery_ui_css'))
      ->set('jquery_ui_js', $form_state->getValue('jquery_ui_js'))
      ->set('jquery_ui_theme', $form_state->getValue('jquery_ui_theme'))
      ->set('jquery_ui_version', $form_state->getValue('jquery_ui_version'))
      ->set('minified', $form_state->getValue('minified'))
      ->save();

    parent::submitForm($form, $form_state);

    // Clear relevant caches.
    $this->cssCollectionOptimizer->deleteAll();
    $this->jsCollectionOptimizer->deleteAll();
    Cache::invalidateTags(['library_info']);
  }

}
