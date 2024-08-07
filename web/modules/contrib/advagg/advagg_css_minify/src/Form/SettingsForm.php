<?php

namespace Drupal\advagg_css_minify\Form;

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
   * The AdvAgg cache.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cache;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /**
     * @var \Drupal\advagg_css_minify\Form\SettingsForm
     */
    $instance = parent::create($container);
    $instance->setCache($container->get('cache.advagg'));

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'advagg_css_minify_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['advagg_css_minify.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\Core\Config\Config $config */
    $config = $this->config('advagg_css_minify.settings');
    $form = [];
    if ($this->config('advagg.settings')->get('cache_level') === 0) {
      $form['advagg_devel_msg'] = [
        '#markup' => '<p>' . $this->t('The settings below will not have any effect because AdvAgg is currently in <a href="@devel">development mode</a>. Once the cache settings have been set to a non-development level, CSS minification will take place.', [
          '@devel' => Url::fromRoute('advagg.settings', [], [
            'fragment' => 'edit-advagg-cache-level',
          ])->toString(),
        ]) . '</p>',
      ];
    }

    $options = [
      0 => $this->t('Disabled'),
      1 => $this->t('Core'),
      2 => $this->t('YUI Compressor'),
    ];
    $form['minifier'] = [
      '#type' => 'radios',
      '#title' => $this->t('Minification: Select a minifier'),
      '#default_value' => $config->get('minifier'),
      '#options' => $options,
    ];
    $form['add_license'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Add licensing comments'),
      '#default_value' => $config->get('add_license'),
      '#description' => $this->t("If unchecked, the Advanced Aggregation module's licensing comments
      will be omitted from the aggregated files. Omitting the comments will produce somewhat better scores in
      some automated security scans but otherwise should not affect your site. These are included by default in order to better follow the spirit of the GPL by providing the source for javascript files."),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\Core\Config\Config $config */
    $config = $this->config('advagg_css_minify.settings');

    // Clear Caches.
    Cache::invalidateTags(['library_info']);
    $this->cache->invalidateAll();

    // Save settings.
    $config->set('add_license', $form_state->getValue('add_license'))
      ->set('minifier', $form_state->getValue('minifier'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
