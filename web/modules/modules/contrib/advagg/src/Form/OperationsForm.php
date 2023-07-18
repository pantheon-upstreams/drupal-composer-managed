<?php

namespace Drupal\advagg\Form;

use Drupal\advagg\AdvaggSettersTrait;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Crypt;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure advagg settings for this site.
 */
class OperationsForm extends ConfigFormBase {

  use AdvaggSettersTrait;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /**
     * @var \Drupal\advagg\Form\OperationsForm
     */
    $instance = parent::create($container);
    $instance->setPrivateKey($container->get('private_key'));
    $instance->setDateFomatter($container->get('date.formatter'));
    $instance->setTime($container->get('datetime.time'));
    $instance->setCache($container->get('cache.advagg'));
    $instance->setFileSystem($container->get('file_system'));

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'advagg_operations';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['advagg.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Explain what can be done on this page.
    $form['tip'] = [
      '#markup' => '<p>' . $this->t('This is a collection of commands to control the cache and to manage testing of this module. In general this page is useful when troubleshooting some aggregation issues. For normal operations, you do not need to do anything on this page below the Smart Cache Flush. There are no configuration options here.') . '</p>',
    ];
    $form['wrapper'] = [
      '#prefix' => "<div id='operations-wrapper'>",
      '#suffix' => "</div>",
    ];

    // Set/Remove Bypass Cookie.
    $form['bypass'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Aggregation Bypass Cookie'),
      '#description' => $this->t('This will set or remove a cookie that disables aggregation for a set period of time.'),
    ];
    $form['bypass']['timespan'] = [
      '#type' => 'select',
      '#title' => $this->t('Bypass length'),
      '#options' => [
        21600 => $this->t('6 hours'),
        43200 => $this->t('12 hours'),
        86400 => $this->t('1 day'),
        172800 => $this->t('2 days'),
        604800 => $this->t('1 week'),
        2592000 => $this->t('1 month'),
        31536000 => $this->t('1 year'),
      ],
    ];
    $form['bypass']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Toggle The "aggregation bypass cookie" For This Browser'),
      '#attributes' => [
        'onclick' => 'javascript:return advagg_toggle_cookie()',
      ],
      '#submit' => ['::toggleBypassCookie'],
    ];
    // Add in aggregation bypass cookie javascript.
    $form['#attached']['drupalSettings']['advagg'] = [
      'key' => Crypt::hashBase64($this->privateKey->get()),
    ];
    $form['#attached']['library'][] = 'advagg/admin.operations';

    // Tasks run by cron.
    $form['cron'] = [
      '#type' => 'details',
      '#open' => TRUE,
      '#title' => $this->t('Cron Maintenance Tasks'),
      'description' => [
        '#markup' => $this->t('The following operation is ran on cron but you can run it manually here.'),
      ],
    ];
    $form['cron']['wrapper'] = [
      '#prefix' => "<div id='cron-wrapper'>",
      '#suffix' => "</div>",
    ];
    $form['cron']['smart_file_flush'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Clear Stale Files'),
      '#description' => $this->t('Scan all files in the css/js optimized directories and remove outdated ones.'),
    ];
    $form['cron']['smart_file_flush']['advagg_flush_stale_files'] = [
      '#type' => 'submit',
      '#value' => $this->t('Remove All Stale Files'),
      '#submit' => ['::clearStaleAggregates'],
      '#ajax' => [
        'callback' => '::cronTasksAjax',
        'wrapper' => 'cron-wrapper',
      ],
    ];

    // Hide drastic measures as they should not be done unless really needed.
    $form['drastic_measures'] = [
      '#type' => 'details',
      '#title' => $this->t('Drastic Measures'),
      '#description' => $this->t('The options below should normally never need to be done.'),
    ];
    $form['drastic_measures']['wrapper'] = [
      '#prefix' => "<div id='drastic-measures-wrapper'>",
      '#suffix' => "</div>",
    ];
    $form['drastic_measures']['dumb_cache_flush'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Clear All Caches'),
      '#description' => $this->t('Remove all entries from the advagg cache and file information stores. Useful if you suspect a cache is not getting cleared.'),
    ];
    $form['drastic_measures']['dumb_cache_flush']['advagg_flush_all_caches'] = [
      '#type' => 'submit',
      '#value' => $this->t('Clear All Caches & File Information'),
      '#submit' => ['::clearAggregates'],
      '#ajax' => [
        'callback' => '::drasticTasksAjax',
        'wrapper' => 'drastic-measures-wrapper',
      ],
    ];
    $form['drastic_measures']['force_change'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Force new files'),
      '#description' => $this->t('Force the creation of all new optimized files by incrementing a global counter. Current value of counter: %value. This is useful if a CDN has cached a file incorrectly as it will force new ones to be used even if nothing else has changed.', [
        '%value' => $this->config('advagg.settings')->get('global_counter'),
      ]),
    ];
    $form['drastic_measures']['force_change']['increment_global_counter'] = [
      '#type' => 'submit',
      '#value' => $this->t('Increment Global Counter'),
      '#submit' => ['::incrementCounter'],
      '#ajax' => [
        'callback' => '::drasticTasksAjax',
        'wrapper' => 'drastic-measures-wrapper',
      ],
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * Report results via Ajax.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   *
   * @return array
   *   The wrapper element.
   */
  public function tasksAjax(array &$form) {
    return $form['wrapper'];
  }

  /**
   * Clear out all advagg cache bins and clear out all advagg aggregated files.
   */
  public function clearAggregates() {
    // Clear out the cache.
    Cache::invalidateTags(['library_info']);
    $this->cache->invalidateAll();
    $pub = $this->fileSystem->realpath('public://');
    $css_count = count(glob($pub . '/css/optimized/*.css'));
    $js_count = count(glob($pub . '/js/optimized/*.js'));
    foreach (['public://js/optimized', 'public://css/optimized'] as $path) {
      if (file_exists($path)) {
        $this->fileSystem->deleteRecursive($path);
      }
    }

    // Report back the results.
    $this->messenger()->addMessage($this->t('All AdvAgg optimized files have been deleted. %css_count CSS files and %js_count JS files have been removed.', [
      '%css_count' => $css_count,
      '%js_count' => $js_count,
    ]));
  }

  /**
   * Clear out all stale advagg aggregated files.
   */
  public function clearStaleAggregates() {
    $counts = advagg_cron(TRUE);

    // Report back the results.
    if (!empty($counts['css']) || !empty($counts['js'])) {
      $this->messenger()->addMessage($this->t('All stale aggregates have been deleted. %css_count CSS files and %js_count JS files have been removed.', [
        '%css_count' => count($counts['css']),
        '%js_count' => count($counts['js']),
      ]));
    }
    else {
      $this->messenger()->addMessage($this->t('No stale aggregates found. Nothing was deleted.'));
    }
  }

  /**
   * Increment the global counter. Also full cache clear.
   */
  public function incrementCounter() {
    // Clear out the cache and delete aggregates.
    $this->clearAggregates();

    // Increment counter.
    $new_value = $this->config('advagg.settings')->get('global_counter') + 1;
    $this->config('advagg.settings')
      ->set('global_counter', $new_value)
      ->save();
    $this->messenger()->addMessage($this->t('Global counter is now set to %new_value', [
      '%new_value' => $new_value,
    ]));
  }

  /**
   * Report results from the drastic measure tasks via Ajax.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   *
   * @return array
   *   The wrapper element.
   */
  public function drasticTasksAjax(array &$form) {
    return $form['drastic_measures']['wrapper'];
  }

  /**
   * Report results from the cron tasks via Ajax.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   *
   * @return array
   *   The wrapper element.
   */
  public function cronTasksAjax(array &$form) {
    return $form['cron']['wrapper'];
  }

  /**
   * Set or remove the AdvAggDisabled cookie.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function toggleBypassCookie(array &$form, FormStateInterface $form_state) {
    $cookie_name = 'AdvAggDisabled';
    $key = Crypt::hashBase64($this->privateKey->get());

    // If the cookie does exist then remove it.
    if (!empty($_COOKIE[$cookie_name]) && $_COOKIE[$cookie_name] == $key) {
      setcookie($cookie_name, '', -1, $GLOBALS['base_path'], '.' . $_SERVER['HTTP_HOST']);
      unset($_COOKIE[$cookie_name]);
      $this->messenger()->addMessage($this->t('AdvAgg Bypass Cookie Removed.'));
    }
    // If the cookie does not exist then set it.
    else {
      setcookie($cookie_name, $key, $this->time->getRequestTime() + $form_state->getValue('timespan'), $GLOBALS['base_path'], '.' . $_SERVER['HTTP_HOST']);
      $_COOKIE[$cookie_name] = $key;
      $this->messenger()->addMessage($this->t('AdvAgg Bypass Cookie Set for %time.', [
        '%time' => $this->dateFormatter->formatInterval($form_state->getValue('timespan')),
      ]));
    }
    $this->clearAggregates();
  }

}
