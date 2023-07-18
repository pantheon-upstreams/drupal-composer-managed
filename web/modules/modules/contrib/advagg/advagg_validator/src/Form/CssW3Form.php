<?php

namespace Drupal\advagg_validator\Form;

use DOMDocument;
use Drupal\advagg\AdvaggSettersTrait;
use Drupal\Core\Form\FormStateInterface;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure form for W3C validation of CSS files.
 */
class CssW3Form extends BaseValidatorForm {

  use AdvaggSettersTrait;

  /**
   * The Guzzle HTTP Client.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * The Drupal renderer.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /**
     * @var \Drupal\advagg_validator\Form\CssW3Form
     */
    $instance = parent::create($container);
    $instance->setRequestStack($container->get('request_stack'));
    $instance->setHttpClient($container->get('http_client'));
    $instance->setRenderer($container->get('renderer'));

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'advagg_validator_cssw3';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::generateForm('css', FALSE);
    $form['notice'] = [
      '#markup' => '<div>' . $this->t('Notice: The form below will submit files to the <a href="http://jigsaw.w3.org/css-validator/">http://jigsaw.w3.org/css-validator/</a> service if used.') . '</div>',
      '#weight' => -1,
    ];
    $form = parent::buildForm($form, $form_state);
    unset($form['actions']);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitCheckAll(array &$form, FormStateInterface $form_state) {
    $dir = $form_state->getTriggeringElement()['#name'];
    $files = [];
    foreach ($form_state->getValues() as $key => $value) {
      if (strpos($key, 'hidden') === FALSE || strpos($value, $dir) === FALSE || ($dir === '.' && substr_count($value, '/') > 0)) {
        continue;
      }
      $files[] = $value;
    }

    // Check list.
    $info = $this->testFiles($files);
    $info = $this->hideGoodFiles($info);

    $output = [
      '#theme' => 'item_list',
      '#items' => $info,
    ];
    $this->messenger()->addMessage($this->renderer->render($output));
  }

  /**
   * Display validation info via ajax callback.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function ajaxCheck(array &$form, FormStateInterface $form_state) {
    $dir = $form_state->getTriggeringElement()['#name'];
    return $this->getElement($form, explode('/', $dir))['wrapper'];
  }

  /**
   * {@inheritdoc}
   */
  public function submitCheckDirectory(array &$form, FormStateInterface $form_state) {
    $dir = $form_state->getTriggeringElement()['#name'];
    $files = [];
    $slash_count = substr_count('/' . $dir, '/');
    foreach ($form_state->getValues() as $key => $value) {
      if (strpos($key, 'hidden') === FALSE || strpos($value, $dir) === FALSE || substr_count($value, '/') > $slash_count || ($dir === '.' && substr_count($value, '/') > 0)) {
        continue;
      }
      $files[] = $value;
    }

    // Check list.
    $info = $this->testFiles($files);
    $info = $this->hideGoodFiles($info);

    $output = [
      '#theme' => 'item_list',
      '#items' => $info,
    ];
    $this->messenger()->addMessage($this->renderer->render($output));
  }

  /**
   * {@inheritdoc}
   */
  protected function testFiles(array $files, array $options = []) {
    $output = [];
    foreach ($files as $filename) {
      // Skip missing files.
      if (!file_exists($filename)) {
        continue;
      }
      $lines = file($filename);

      // Run jigsaw.w3.org validator.
      $output[$filename]['jigsaw.w3.org'] = $this->testW3C($filename, $options);

      // Get extra context for errors.
      if (!empty($output[$filename]['jigsaw.w3.org']['errors'])) {
        foreach ($output[$filename]['jigsaw.w3.org']['errors'] as &$value) {
          if (isset($value['line'])) {
            $value['linedata'] = $lines[($value['line'] - 1)];
            if (strlen($value['linedata']) > 512) {
              unset($value['linedata']);
            }
          }
        }
        unset($value);
      }
      if (!empty($output[$filename]['jigsaw.w3.org']['warnings'])) {
        foreach ($output[$filename]['jigsaw.w3.org']['warnings'] as &$value) {
          if (isset($value['line'])) {
            $value['linedata'] = $lines[$value['line'] - 1];
            if (strlen($value['linedata']) > 512) {
              unset($value['linedata']);
            }
          }
        }
        unset($value);
      }
    }
    return $output;
  }

  /**
   * Given a CSS file, test to make sure it is valid CSS.
   *
   * @param string $filename
   *   The name of the file.
   * @param array $validator_options
   *   List of options to pass along to the CSS Validator.
   *
   * @return array
   *   Info from the w3c server.
   */
  private function testW3C($filename, array &$validator_options = []) {
    // Get CSS files contents.
    $validator_options['text'] = file_get_contents($filename);
    if (strlen($validator_options['text']) > 50000) {
      unset($validator_options['text']);
      $validator_options['uri'] = $this->requestStack->getCurrentRequest()->getBaseUrl() . $filename;
    }

    // Add in defaults.
    $validator_options += [
      'output' => 'soap12',
      'warning' => '1',
      'profile' => 'css3',
      'usermedium' => 'all',
      'lang' => 'en',
    ];

    // Build request URL.
    // API Documentation http://jigsaw.w3.org/css-validator/api.html
    $request_url = 'http://jigsaw.w3.org/css-validator/validator';
    $query = http_build_query($validator_options, '', '&');
    $url = $request_url . '?' . $query;
    try {
      $data = $this->httpClient
        ->get($url)
        ->getBody();
    }
    catch (RequestException $e) {
      watchdog_exception('AdvAgg Validator', $e);
    }
    catch (\Exception $e) {
      watchdog_exception('AdvAgg Validator', $e);
    }
    if (!empty($data)) {
      // Parse XML and return info.
      $return = $this->parseSoapResponse($data);
      $return['filename'] = $filename;
      if (isset($validator_options['text'])) {
        unset($validator_options['text']);
      }
      elseif (isset($validator_options['uri'])) {
        unset($validator_options['uri']);
      }
      $return['options'] = $validator_options;
      return $return;
    }

    return ['error' => $this->t('W3C Server did not return a 200 or request data was empty.')];
  }

  /**
   * {@inheritdoc}
   */
  private function parseSoapResponse($xml) {
    $doc = new DOMDocument();
    $response = [];

    // Try to load soap 1.2 XML response, and suppress warning reports if any.
    if (!@$doc->loadXML($xml)) {
      // Could not load the XML document.
      return $response;
    }

    // Get the standard CDATA elements.
    $cdata = ['uri', 'checkedby', 'csslevel', 'date'];
    foreach ($cdata as $var) {
      $element = $doc->getElementsByTagName($var);
      if ($element->length) {
        $response[$var] = $element->item(0)->nodeValue;
      }
    }

    // Handle the element validity and get errors if not valid.
    $element = $doc->getElementsByTagName('validity');
    if ($element->length && $element->item(0)->nodeValue === 'true') {
      $response['validity'] = TRUE;
    }
    else {
      $response['validity'] = FALSE;
      $errors = $doc->getElementsByTagName('error');
      foreach ($errors as $error) {
        $response['errors'][] = $this->domExtractor($error);
      }
    }

    // Get warnings.
    $warnings = $doc->getElementsByTagName('warning');
    foreach ($warnings as $warning) {
      $response['warnings'][] = $this->domExtractor($warning);
    }

    // Return response array.
    return $response;
  }

}
