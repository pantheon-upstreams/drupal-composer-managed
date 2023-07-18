<?php

namespace Drupal\advagg_ext_minify\Form;

use Drupal\advagg\AdvaggSettersTrait;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure advagg_ext_minify settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  use AdvaggSettersTrait;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /**
     * @var \Drupal\advagg_ext_minify\Form\SettingsForm
     */
    $instance = parent::create($container);
    $instance->setCache($container->get('cache.advagg'));

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'advagg_ext_minify_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['advagg_ext_minify.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = [];

    // CSS command line.
    $this->generateForm($form, ['css', $this->t('CSS')]);

    // JS command line.
    $this->generateForm($form, ['js', $this->t('JavaScript')]);
    return parent::buildForm($form, $form_state);
  }

  /**
   * Generate a form for css or js depending on the input.
   *
   * @param array $form
   *   The form array to add to.
   * @param array $params
   *   An array where:
   *     key 0 is the machine name.
   *     key 1 is the title.
   */
  private function generateForm(array &$form, array $params) {
    $form[$params[0]] = [
      '#type' => 'fieldset',
      '#title' => $params[1],
    ];
    $form[$params[0]]['cmd'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Command Line'),
    ];

    $description = $this->t('{%CWD%} = \Drupal::root(). <br /> {%IN%} = input file. <br /> {%IN_URL_ENC%} = url pointing to the input file that has been url encoded. <br /> {%OUT%} = output file. <br /><br />');
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
      $description .= ' ' . $this->t('Example using the <a href="@link1">Microsoft Ajax Minifier</a>. <p><code>@code1</code></p>', [
        '@link1' => 'http://ajaxmin.codeplex.com/',
        '@code1' => 'AjaxMinifier {%IN%} -o {%OUT%}',
      ]);
    }

    if ($params[0] === 'js') {
      $description .= ' ' . $this->t('Example using the <a href="@link1">Google Closure Compiler</a>. <p><code>@code1</code></p>', [
        '@link1' => 'https://developers.google.com/closure/compiler/docs/gettingstarted_app',
        '@code1' => 'java -jar compiler.jar --js {%CWD%}/{%IN%} --js_output_file {%OUT%}',
      ]);

      $description .= ' ' . $this->t('Example using curl to minify via the <a href="@link1">Online Google Closure Compiler</a>. <p><code>@code1</code></p>', [
        '@link1' => 'https://developers.google.com/closure/compiler/docs/api-ref',
        '@code1' => 'curl -o {%OUT%} -d output_info=compiled_code -d code_url={%IN_URL_ENC%} http://closure-compiler.appspot.com/compile',
      ]);
    }
    if ($params[0] === 'css') {
      $description .= ' ' . $this->t('Example using the <a href="@link1">YUI Compressor</a>. <p><code>@code1</code></p>', [
        '@link1' => 'http://yui.github.io/yuicompressor/',
        '@code1' => 'java -jar yuicompressor-x.y.z.jar --type css --line-break 4096 {%CWD%}/{%IN%} -o {%OUT%}',
      ]);

      $description .= ' ' . $this->t('Example using curl to minify via an online <a href="@link1">CSS Minifier</a>. <p><code>@code1</code></p>', [
        '@link1' => 'http://cnvyr.io/',
        '@code1' => 'curl -o {%OUT%} -F \'files0=@{%IN%}\' http://srv.cnvyr.io/v1?min=css',
      ]);
    }

    $form[$params[0]]['cmd'][$params[0] . '_cmd'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Command to run'),
      '#default_value' => $this->config('advagg_ext_minify.settings')->get($params[0] . '_cmd'),
      '#description' => $description,
    ];

    $form[$params[0]]['cmd'][$params[0] . '_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable external @type minification', ['@type' => $params[1]]),
      '#default_value' => $this->config('advagg_ext_minify.settings')->get($params[0] . '_cmd'),
    ];

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('advagg_ext_minify.settings')
      ->set('js_cmd', $form_state->getValue('js_cmd'))
      ->set('js_enabled', $form_state->getValue('js_enabled'))
      ->set('css_cmd', $form_state->getValue('css_cmd'))
      ->set('css_enabled', $form_state->getValue('css_enabled'))
      ->save();
    parent::submitForm($form, $form_state);

    // Clear relevant caches.
    Cache::invalidateTags([
      'library_info',
    ]);
  }

}
