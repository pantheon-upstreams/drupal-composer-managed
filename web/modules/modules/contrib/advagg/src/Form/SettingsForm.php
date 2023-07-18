<?php

namespace Drupal\advagg\Form;

use Drupal\advagg\AdvaggSettersTrait;
use Drupal\advagg\Asset\AssetOptimizer;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure advagg settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  use AdvaggSettersTrait;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /**
     * @var \Drupal\advagg\Form\SettingsForm
     */
    $instance = parent::create($container);
    $instance->setDateFomatter($container->get('date.formatter'));
    $instance->setState($container->get('state'));
    $instance->setModuleHandler($container->get('module_handler'));
    $instance->setCache($container->get('cache.advagg'));
    $instance->setTime($container->get('datetime.time'));

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'advagg_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['advagg.settings', 'system.performance'];
  }

  /**
   * The getter options for cache level.
   *
   * @return array
   *   Return a array with options.
   */
  public function getCacheLevelOptions() :array {
    return [
      0 => $this->t('Development'),
      1 => $this->t('Low'),
      2 => $this->t('Normal'),
      3 => $this->t('High'),
    ];
  }

  /**
   * Getter for short times.
   *
   * @return array
   *   Return array with short times.
   */
  public function getShortTimes() :array {
    return [
      900 => $this->t('15 minutes'),
      1800 => $this->t('30 minutes'),
      2700 => $this->t('45 minutes'),
      3600 => $this->t('1 hour'),
      7200 => $this->t('2 hours'),
      14400 => $this->t('4 hours'),
      21600 => $this->t('6 hours'),
      43200 => $this->t('12 hours'),
      64800 => $this->t('18 hours'),
      86400 => $this->t('1 day'),
      172800 => $this->t('2 days'),
    ];
  }

  /**
   * Getter for long times.
   *
   * @return array
   *   Return array with long times.
   */
  public function getLongTimes() :array {
    return [
      172800 => $this->t('2 days'),
      259200 => $this->t('3 days'),
      345600 => $this->t('4 days'),
      432000 => $this->t('5 days'),
      518400 => $this->t('6 days'),
      604800 => $this->t('1 week'),
      1209600 => $this->t('2 week'),
      1814400 => $this->t('3 week'),
      2592000 => $this->t('1 month'),
      3628800 => $this->t('6 weeks'),
      4838400 => $this->t('2 months'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('advagg.settings');
    $form['global'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Global Options'),
    ];
    $form['global']['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable advanced aggregation'),
      '#default_value' => $config->get('enabled'),
      '#description' => $this->t('Uncheck this box to temporarily disable AdvAgg functionality.'),
    ];
    $form['global']['dns_prefetch'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use DNS Prefetch for external CSS/JS.'),
      '#default_value' => $config->get('dns_prefetch'),
      '#description' => $this->t('Start the DNS lookup for external CSS and JavaScript files as soon as possible.'),
    ];
    $form['global']['server_config'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Server Config'),
      'immutable_group' => [
        'title' => [
          '#type' => 'html_tag',
          '#tag' => 'strong',
          '#value' => $this->t('Cache-Control: Immutable'),
        ],
        'information' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->t('Your server can send <a href="@url1">Cache-Control: immutable</a> header for all optimized files. This should improve performance for some users. Current <a href="@url2">browser support</a>', [
            '@url1' => 'http://bitsup.blogspot.de/2016/05/cache-control-immutable.html',
            '@url2' => 'https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control#Browser_compatibility',
          ]),
        ],
        'immutable' => [
          '#type' => 'checkbox',
          '#title' => $this->t('Include Cache-Control: immutable in generated .htaccess files.'),
          '#default_value' => $config->get('immutable'),
          '#description' => $this->t('With the Apache server, AdvAgg can generate config to send the header for all optimized files.'),
        ],
      ],
    ];
    if (stripos($_SERVER['SERVER_SOFTWARE'], 'nginx') !== FALSE) {
      $form['global']['server_config']['immutable_group']['immutable']['#access'] = FALSE;
      $form['global']['server_config']['immutable_group']['nginx'] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t("With Nginx, AdvAgg can't set the headers for optimized files in a performant manner. However, you can easily do so in your server config. See the <a href='@url'>manual for instructions</a>.", [
          '@url' => 'https://www.drupal.org/docs/8/modules/advanced-cssjs-aggregation/advanced-aggregates#server-settings',
        ]),
      ];
    }

    $form['global']['cache_level'] = [
      '#type' => 'radios',
      '#title' => $this->t('AdvAgg Cache Settings'),
      '#default_value' => $config->get('cache_level'),
      '#options' => $this->getCacheLevelOptions(),
      '#description' => $this->t("No performance data yet but most use cases will probably want to use the Normal cache mode.", [
        '@information' => Url::fromRoute('advagg.info')->toString(),
      ]),
    ];

    $form['global']['dev_container'] = [
      '#type' => 'container',
      '#states' => [
        'visible' => [
          ':input[name="cache_level"]' => ['value' => '-1'],
        ],
      ],
    ];

    $form['compression'] = [
      '#type' => 'details',
      '#open' => TRUE,
      '#title' => $this->t('Compression Options'),
      '#description' => $this->t('Compressed files will automatically be served by the Apache server'),
    ];
    if (stripos($_SERVER['SERVER_SOFTWARE'], 'nginx') !== FALSE) {
      $form['compression']['#description'] = $this->t("AdvAgg can't configure your Nginx server to automatically serve compressed assets. See the <a href='@url'>AdvAgg manual</a> for instructions on manually doing so.", [
        '@url' => 'https://www.drupal.org/docs/8/modules/advanced-cssjs-aggregation/advanced-aggregates#server-settings',
      ]);
    }
    $form['compression']['css_gzip'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Gzip CSS assets'),
      '#default_value' => $this->config('system.performance')->get('css.gzip'),
      '#description' => $this->t('This should be enabled unless you are experiencing corrupted compressed asset files.'),
    ];
    $form['compression']['js_gzip'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Gzip JavaScript assets'),
      '#default_value' => $this->config('system.performance')->get('js.gzip'),
      '#description' => $this->t('This should be enabled unless you are experiencing corrupted compressed asset files.'),
    ];
    $brotli_available = function_exists('brotli_compress');
    $brotli_message = ($brotli_available) ? $this->t("Select to compress this asset type with brotli compression. See <a href='https://github.com/kjdev/php-ext-brotli'>PHP Brotli</a> page for more information.") : $this->t("Brotli compression is not available on your server. See <a href='https://github.com/kjdev/php-ext-brotli'>PHP Brotli</a> page for more information.");
    $form['compression']['css_brotli'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Brotli compress CSS assets'),
      '#default_value' => $config->get('css.brotli'),
      '#description' => $brotli_message,
      '#disabled' => !$brotli_available,
    ];
    $form['compression']['js_brotli'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Brotli compress JavaScript assets'),
      '#default_value' => $config->get('js.brotli'),
      '#description' => $brotli_message,
      '#disabled' => !$brotli_available,
    ];

    $form['css'] = [
      '#type' => 'details',
      '#title' => $this->t('CSS Options'),
      '#open' => TRUE,
    ];
    $form['css']['css_combine_media'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Combine CSS files by using media queries'),
      '#default_value' => $config->get('css.combine_media'),
      '#description' => $this->t('Will combine more CSS files together because different CSS media types can be used in the same file by using media queries. Use cores grouping logic needs to be unchecked in order for this to work. Also noted is that due to an issue with IE9, compatibility mode is forced off if this is enabled.'),
    ];
    $form['css']['css_fix_type'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Fix improperly set type'),
      '#default_value' => $config->get('css.fix_type'),
      '#description' => $this->t('If type is external but does not start with http, https, or // change it to be type file. If type is file but it starts with http, https, or // change type to be external. Note that if this is causing issues, odds are you have a double slash when there should be a single; see <a href="@link">this issue</a>', [
        '@link' => 'https://www.drupal.org/node/2336217',
      ]),
    ];
    $form['css']['css_preserve_external'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Do not change external to file if on same host.'),
      '#default_value' => $config->get('css.preserve_external'),
      '#description' => $this->t('If a CSS file is set as external and is on the same hosts do not convert to file.'),
      '#states' => [
        'disabled' => [
          '#edit-css-fix-type' => ['checked' => FALSE],
        ],
      ],
    ];

    $form['js'] = [
      '#type' => 'details',
      '#title' => $this->t('JS Options'),
      '#open' => TRUE,
    ];
    $form['js']['js_fix_type'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Fix improperly set type'),
      '#default_value' => $config->get('js.fix_type'),
      '#description' => $this->t('If type is external but does not start with http, https, or // change it to be type file. If type is file but it starts with http, https, or // change type to be external. Note that if this is causing issues, odds are you have a double slash when there should be a single; see <a href="@link">this issue</a>', [
        '@link' => 'https://www.drupal.org/node/2336217',
      ]),
    ];
    $form['js']['js_preserve_external'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Do not change external to file if on same host.'),
      '#default_value' => $config->get('js.preserve_external'),
      '#description' => $this->t('If a JS file is set as external and is on the same hosts do not convert to file.'),
      '#states' => [
        'disabled' => [
          '#edit-js-fix-type' => ['checked' => FALSE],
        ],
      ],
    ];

    $form['cron'] = [
      '#type' => 'details',
      '#title' => $this->t('Cron Options'),
      '#description' => $this->t('Unless you have a good reason to adjust these values you should leave them alone.'),
    ];

    $last_ran = $this->state->get('advagg.cron_timestamp', NULL);
    if ($last_ran) {
      $last_ran = $this->t('@time ago', ['@time' => $this->dateFormatter->formatInterval($this->time->getRequestTime() - $last_ran)]);
    }
    else {
      $last_ran = $this->t('never');
    }
    $form['cron']['cron_frequency'] = [
      '#type' => 'select',
      '#options' => $this->getShortTimes(),
      '#title' => 'Minimum amount of time between advagg_cron() runs.',
      '#default_value' => $config->get('cron_frequency'),
      '#description' => $this->t('The default value for this is %value. The last time advagg_cron was ran is %time.', [
        '%value' => $this->dateFormatter->formatInterval($config->get('cron_frequency')),
        '%time' => $last_ran,
      ]),
    ];

    $form['cron']['stale_file_threshold'] = [
      '#type' => 'select',
      '#options' => $this->getLongTimes(),
      '#title' => 'Delete aggregates modified more than a set time ago.',
      '#default_value' => $this->config('system.performance')->get('stale_file_threshold'),
      '#description' => $this->t('The default value for this is %value.', [
        '%value' => $this->dateFormatter->formatInterval($this->config('system.performance')->getOriginal('stale_file_threshold')),
      ]),
    ];

    $form['obscure'] = [
      '#type' => 'details',
      '#title' => $this->t('Obscure Options'),
      '#description' => $this->t('Some of the more obscure AdvAgg settings. Odds are you do not need to change anything in here.'),
    ];
    $form['obscure']['path_convert_absolute_to_protocol_relative'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Convert absolute paths to be protocol relative paths.'),
      '#default_value' => $config->get('path.convert.absolute_to_protocol_relative'),
      '#description' => $this->t('If the src to a CSS/JS file points starts with http:// or https://, convert it to use a protocol relative path //. Will also convert url() references inside of css files.'),
      '#states' => [
        'enabled' => [
          '#edit-path-convert-force-https' => ['checked' => FALSE],
          '#edit-path-convert-absolute' => ['checked' => FALSE],
        ],
      ],
    ];
    $form['obscure']['path_convert_absolute'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Convert relative paths to be absolute paths.'),
      '#default_value' => $config->get('path.convert.absolute'),
      '#description' => $this->t('If the src to a CSS/JS file points starts with a relative path / convert to absolute.'),
      '#states' => [
        'enabled' => [
          '#edit-path-convert-force-https' => ['checked' => FALSE],
          '#edit-path-convert-absolute-to-protocol-relative' => ['checked' => FALSE],
        ],
      ],
    ];
    $form['obscure']['path_convert_force_https'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Convert http:// to https://.'),
      '#default_value' => $config->get('path.convert.force_https'),
      '#description' => $this->t('If the src to a CSS/JS file starts with http:// convert it https://. Will also convert url() references inside of css files.'),
      '#states' => [
        'enabled' => [
          '#edit-path-convert-absolute-to-protocol-relative' => ['checked' => FALSE],
          '#edit-path-convert-absolute' => ['checked' => FALSE],
        ],
      ],
    ];
    $form['obscure']['symlinks'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use "Options +FollowSymLinks"'),
      '#default_value' => $config->get('symlinks'),
      '#description' => $this->t('Some shared hosts require "<code>Options +FollowSymLinks</code>" in the .htaccess for asset directories.'),
      '#states' => [
        'enabled' => [
          '#edit-symlinksifownermatch' => ['checked' => FALSE],
        ],
      ],
    ];
    $form['obscure']['symlinksifownermatch'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use "Options +SymLinksIfOwnerMatch"'),
      '#default_value' => $config->get('symlinksifownermatch'),
      '#description' => $this->t('Some shared hosts require "<code>Options +SymLinksIfOwnerMatch</code>" in the .htaccess for asset directories.'),
      '#states' => [
        'enabled' => [
          '#edit-symlinks' => ['checked' => FALSE],
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
    $config = $this->config('advagg.settings');
    $htaccess = FALSE;
    if ($config->get('immutable') != $form_state->getValue('immutable')) {
      $htaccess = TRUE;
    }
    elseif ($config->get('symlinks') != $form_state->getValue('symlinks')) {
      $htaccess = TRUE;
    }
    elseif ($config->get('symlinksifownermatch') != $form_state->getValue('symlinksifownermatch')) {
      $htaccess = TRUE;
    }
    $config
      ->set('css.brotli', $form_state->getValue('css_brotli'))
      ->set('css.fix_type', $form_state->getValue('css_fix_type'))
      ->set('css.combine_media', $form_state->getValue('css_combine_media'))
      ->set('css.preserve_external', $form_state->getValue('css_preserve_external'))
      ->set('path.convert.force_https', $form_state->getValue('path_convert_force_https'))
      ->set('path.convert.absolute', $form_state->getValue('path_convert_absolute'))
      ->set('path.convert.absolute_to_protocol_relative', $form_state->getValue('path_convert_absolute_to_protocol_relative'))
      ->set('enabled', $form_state->getValue('enabled'))
      ->set('dns_prefetch', $form_state->getValue('dns_prefetch'))
      ->set('cache_level', $form_state->getValue('cache_level'))
      ->set('cron_frequency', $form_state->getValue('cron_frequency'))
      ->set('js.brotli', $form_state->getValue('js_brotli'))
      ->set('js.fix_type', $form_state->getValue('js_fix_type'))
      ->set('js.preserve_external', $form_state->getValue('js_preserve_external'))
      ->set('immutable', $form_state->getValue('immutable'))
      ->set('symlinks', $form_state->getValue('symlinks'))
      ->set('symlinksifownermatch', $form_state->getValue('symlinksifownermatch'))
      ->save();
    $this->config('system.performance')
      ->set('stale_file_threshold', $form_state->getValue('stale_file_threshold'))
      ->set('css.gzip', $form_state->getValue('css_gzip'))
      ->set('js.gzip', $form_state->getValue('js_gzip'))
      ->save();

    // If changed options regenerate the .htaccess files.
    if ($htaccess) {
      AssetOptimizer::generateHtaccess('css', TRUE);
      AssetOptimizer::generateHtaccess('js', TRUE);
    }

    // Clear relevant caches.
    $this->cache->invalidateAll();
    Cache::invalidateTags(['library_info']);

    parent::submitForm($form, $form_state);
  }

}
