<?php

namespace Drupal\advagg\Form;

use Drupal\advagg\AdvaggSettersTrait;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * View AdvAgg information for this site.
 */
class InfoForm extends ConfigFormBase {

  use AdvaggSettersTrait;

  /**
   * The theme registry service.
   *
   * @var \Drupal\Core\Theme\Registry
   */
  protected $themeRegistry;

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * The string translation service.
   *
   * @var \Drupal\Core\StringTranslation\Translator\TranslatorInterface
   */
  protected $translation;

  /**
   * The AdvAgg cache.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cache;

  /**
   * The Messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /**
     * @var \Drupal\advagg\Form\InfoForm
     */
    $instance = parent::create($container);
    $instance->setThemeRegistry($container->get('theme.registry'));
    $instance->setRequestStack($container->get('request_stack'));
    $instance->setDateFomatter($container->get('date.formatter'));
    $instance->setStringTranslation($container->get('string_translation'));
    $instance->setCache($container->get('cache.advagg'));

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'advagg_info';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['tip'] = [
      '#markup' => '<p>' . $this->t('This page provides debugging information. There are no configuration options here.') . '</p>',
    ];

    // Get all hooks and variables.
    $core_hooks = $this->themeRegistry->get();
    $advagg_hooks = advagg_hooks_implemented();

    // Output html preprocess functions hooks.
    $form['theme_info'] = [
      '#type' => 'details',
      '#open' => TRUE,
      '#title' => $this->t('Hook Theme Info'),
    ];
    $data = implode("\n", $core_hooks['html']['preprocess functions']);
    $form['theme_info']['advagg_theme_info'] = [
      '#markup' => '<p>preprocess functions on html.</p><pre>' . $data . '</pre>',
    ];

    $form['hooks_implemented'] = [
      '#type' => 'details',
      '#open' => TRUE,
      '#title' => $this->t('Core asset hooks implemented by modules'),
    ];

    // Output all advagg hooks implemented.
    foreach ($advagg_hooks as $hook => $values) {
      if (empty($values)) {
        $form['hooks_implemented'][$hook] = [
          '#markup' => '<div><strong>' . $hook . ':</strong> 0</div>',
        ];
      }
      else {
        $form['hooks_implemented'][$hook] = [
          '#markup' => '<div><strong>' . $hook . ':</strong> ' . count($values) . $this->formatList($values) . '</div>',
        ];
      }
    }

    // Get info about a file.
    $form['get_info_about_agg'] = [
      '#type' => 'details',
      '#open' => TRUE,
      '#title' => $this->t('Get detailed info about an optimized file'),
    ];
    $form['get_info_about_agg']['filename'] = [
      '#type' => 'textfield',
      '#size' => 170,
      '#maxlength' => 256,
      '#default_value' => '',
      '#title' => $this->t('Filename'),
    ];
    $form['get_info_about_agg']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Lookup Details'),
      '#submit' => ['::getFileInfoSubmit'],
      '#validate' => ['::getFileInfoValidate'],
      '#ajax' => [
        'callback' => '::getFileInfoAjax',
        'wrapper' => 'advagg-file-info-ajax',
        'effect' => 'fade',
      ],
    ];
    if ($tip = $this->getRandomFile()) {
      $form['get_info_about_agg']['tip'] = [
        '#markup' => '<p>' . $this->t('Input an optimized filename like "@css_file".', [
          '@css_file' => $tip,
        ]) . '</p>',
      ];
    }
    $form['get_info_about_agg']['wrapper'] = [
      '#prefix' => "<div id='advagg-file-info-ajax'>",
      '#suffix' => "</div>",
    ];
    $form = parent::buildForm($form, $form_state);
    unset($form['actions']);
    return $form;
  }

  /**
   * Format an indented list from array.
   *
   * @param array $list
   *   The array to convert to a string.
   * @param int $depth
   *   (optional) Depth multiplier for indentation.
   *
   * @return string
   *   The imploded and spaced array.
   */
  private function formatList(array $list, $depth = 1) {
    $spacer = '<br />' . str_repeat('&nbsp;', 2 * $depth);
    $output = $spacer . Xss::filter(implode($spacer, $list), ['br']);
    return $output;
  }

  /**
   * Display file info in a drupal message.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function getFileInfoSubmit(array &$form, FormStateInterface $form_state) {
    $this->messenger()->addMessage($this->getFileInfo($form_state->getValue('filename')));
  }

  /**
   * Display file info via ajax callback.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   *
   * @return array
   *   The file info element.
   */
  public function getFileInfoAjax(array &$form) {
    return $form['get_info_about_agg']['wrapper'];
  }

  /**
   * Verify that the filename is correct.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function getFileInfoValidate(array $form, FormStateInterface $form_state) {
    if (empty($form_state->getValue('filename'))) {
      $form_state->setErrorByName('filename', $this->t('Please input a valid optimized filename.'));
    }
  }

  /**
   * Get detailed info about the given filename.
   *
   * @param string $filename
   *   Name of file to lookup.
   *
   * @return string
   *   Detailed info about this file.
   */
  private function getFileInfo($filename) {
    if (substr_compare($filename, 'css_', 0) || substr_compare($filename, 'js_', 0)) {
      $cid = str_replace(['css_', 'js_', '.css', '.js'], '', $filename);
      $cid = substr($cid, 0, strpos($cid, '.'));
      if ($cached = $this->cache->get($cid, TRUE)) {
        return print_r($cached->data, TRUE);
      }
    }
    return $this->t('Optimized file information not found, confirm spelling of the path. Alternatively, that could be an outdated file.');
  }

  /**
   * Get a (pseudo) random optimized file name.
   *
   * @return bool|string
   *   The filename or FALSE if no valid files found.
   */
  private function getRandomFile() {
    // Ensure the directory exists.
    $dir = 'public://js/optimized/';
    if (!is_dir($dir)) {
      return FALSE;
    }
    if ($handler = opendir($dir)) {
      while (($file = readdir($handler)) !== FALSE) {
        if (is_file($dir . $file) && pathinfo($file, PATHINFO_EXTENSION) == 'js') {
          closedir($handler);
          return $file;
        }
      }
      closedir($handler);
    }
    return FALSE;
  }

}
