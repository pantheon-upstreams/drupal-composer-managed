<?php

namespace Drupal\advagg_validator\Form;

use Drupal\advagg\Form\AdvaggFormBase;
use Drupal\Component\Render\HtmlEscapedText;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base form for all advagg validator options.
 */
abstract class BaseValidatorForm extends AdvaggFormBase {

  /**
   * The StreamWrapper manage.
   *
   * @var \Drupal\Core\StreamWrapper\StreamWrapperManagerInterface
   */
  protected $streamWrapperManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    /**
     * @var \Drupal\advagg_validator\Form\BaseValidatorForm
     */
    $instance = parent::create($container);
    $instance->streamWrapperManager = $container->get('stream_wrapper_manager');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['advagg_validator.settings'];
  }

  /**
   * Generate a hierarchical form sorted by path from asset files.
   *
   * @param string $type
   *   The asset extension - usually 'css' or 'js'.
   * @param bool $run_client_side
   *   Determines whether to assign submit functions to buttons.
   *
   * @return array
   *   A Form API array.
   */
  public function generateForm($type, $run_client_side = TRUE) {
    $form = [];
    $files = $this->scanAllDirs($type);
    rsort($files);
    foreach ($files as $file) {
      $dir = dirname($file);

      // Build the directory structure.
      $levels = explode('/', $dir === '.' ? '{ROOT}' : $dir);
      $point = &$form;
      $built = [];
      foreach ($levels as $key => $value) {
        // Build directory structure.
        $built[] = $value;
        $point = &$point[$value];
        if (!is_array($point)) {
          $form_api_dirname = str_replace(['/', '.'], ['__', '--'], $dir);
          $wrapper = 'advagg-validator-' . $type . '-validator-ajax' . $form_api_dirname;

          $point = [
            '#type' => 'details',
            '#title' => $value,
            '#description' => '<strong>' . $this->t('Directory:') . ' </strong>' . implode('/', $built),
            '#weight' => 100,
          ];
          if (!isset($point['check_all_levels']) && $value !== '{ROOT}' && count($levels) != $key + 1) {
            $point['check_all_levels'] = [
              '#type' => 'submit',
              '#value' => $this->t('Check directory and all subdirectories'),
              '#name' => implode('/', $built),
            ];
            if (!$run_client_side) {
              $point['check_all_levels'] += [
                '#submit' => ['::submitCheckAll'],
                '#ajax' => [
                  'callback' => '::ajaxCheck',
                  'wrapper' => $wrapper,
                ],
              ];
            }
            else {
              $point['check_all_levels'] += [
                '#attributes' => [
                  'class' => ['advagg_validator_recursive_' . $type],
                ],
              ];
            }
          }
          $point['break'] = [
            '#markup' => '<div></div>',
          ];

          $point['wrapper'] = [
            '#markup' => "<div id='" . $wrapper . "' class='results'></div>",
            '#weight' => 90,
          ];
        }

        // Drop in button and info if we reached the point where a file lives.
        if (count($levels) == $key + 1) {
          $form_api_filename = str_replace(['/', '.'], ['__', '--'], $file);

          if (!isset($point['check_this_level'])) {
            $point['check_this_level'] = [
              '#type' => 'submit',
              '#value' => $this->t('Check directory'),
            ];
            if (!$run_client_side) {
              $point['check_this_level'] += [
                '#submit' => ['::submitCheckDirectory'],
                '#name' => $dir,
                '#ajax' => [
                  'callback' => '::ajaxCheck',
                  'wrapper' => $wrapper,
                ],
              ];
            }
            else {
              $point['check_this_level'] += [
                '#attributes' => [
                  'class' => ['advagg_validator_' . $type],
                ],
              ];
            }
          }
          if (!isset($point['start'])) {
            $point['start'] = [
              '#markup' => '<br /><strong>' . $this->t('File:') . ' </strong><div class="filenames">',
            ];
          }
          else {
            $point['start'] = [
              '#markup' => '<br /><strong>' . $this->t('Files:') . ' </strong><div class="filenames">',
            ];
          }
          $point[$form_api_filename] = [
            '#markup' => $file . " </br>\n",
          ];
          if (!isset($point['end'])) {
            $point['end'] = [
              '#markup' => '</div>',
            ];
          }

          $point['hidden_' . $form_api_filename] = [
            '#type' => 'hidden',
            '#value' => $file,
            '#attributes' => [
              'class' => ['filenames'],
            ],
          ];

        }
      }
    }
    return $form;
  }

  /**
   * Do not display info on a file if it is valid.
   *
   * @param array $info
   *   Array containing info about validators and if the file is valid.
   *
   * @return array
   *   $info array minus data if that file is valid.
   */
  protected function hideGoodFiles(array $info) {
    $output = [];
    foreach ($info as $filename => $validators) {
      foreach ($validators as $v_name => $v_data) {
        $output[$filename][$v_name] = ['#prefix' => '<em>' . $filename . ':</em> '];
        if (!empty($v_data['validity'])) {
          $output[$filename][$v_name]['#markup'] = $this->t('valid');
        }
        elseif (isset($v_data['error'])) {
          $output[$filename][$v_name]['error'] = $v_data['error'];
        }
        else {
          $output[$filename][$v_name]['options'] = [
            '#markup' => '<em>' . $this->t('Options:') . '</em><br/>',
          ];
          foreach ($v_data['options'] as $option => $value) {
            $output[$filename][$v_name]['options'][] = [
              '#markup' => new HtmlEscapedText($option) . ': ' . new HtmlEscapedText($value),
              '#suffix' => '</br>',
            ];
          }
          if (isset($v_data['errors'])) {
            $output[$filename][$v_name]['errors'] = [
              '#markup' => '<em>' . $this->t('Errors:') . '</em>',
            ];
            foreach ($v_data['errors'] as $error) {
              $output[$filename][$v_name]['errors'][] = [
                '#prefix' => '<pre>',
                '#plain_text' => print_r($error, TRUE),
                '#suffix' => '</pre>',
              ];
            }
          }
          if (isset($v_data['warnings'])) {
            $output[$filename][$v_name]['warnings'] = [
              '#markup' => '<em>' . $this->t('Warnings:') . '</em>',
            ];
            foreach ($v_data['warnings'] as $warning) {
              $output[$filename][$v_name]['warnings'][] = [
                '#prefix' => '<pre>',
                '#plain_text' => print_r($warning, TRUE),
                '#suffix' => '</pre>',
              ];
            }
          }
        }
      }
    }
    return $output;
  }

  /**
   * Recursively scan the drupal webroot for files matching the given extension.
   *
   * @param string $ext
   *   Usually css or js.
   *
   * @return array
   *   An array of files.
   */
  protected function scanAllDirs($ext) {
    $options = [
      'nodirmask' => '/(\.git|.*\/files*)/',
    ];
    $output = $this->scanDirectory(\Drupal::root(), '/.*\.' . $ext . '$/', $options);
    $files = [];
    foreach ($output as $values) {
      $files[] = str_replace(\Drupal::root() . '/', '', $values->uri);
    }
    return $files;
  }

  /**
   * Finds all files that match a given mask in a given directory.
   *
   * Directories and files beginning with a period are excluded; this
   * prevents hidden files and directories (such as SVN working directories)
   * from being scanned.
   *
   * @param string $dir
   *   The base directory or URI to scan, without trailing slash.
   * @param string $mask
   *   The preg_match() regular expression of the files to find.
   * @param array $options
   *   An associative array of additional options, with the following elements:
   *   - 'nomask': The preg_match() regular expression of the files to ignore.
   *     Defaults to '/(\.\.?|CVS)$/'.
   *   - 'nomask': The preg_match() regular expression of the dirs to ignore.
   *     Defaults to '/(\.git)/'.
   *   - 'callback': The callback function to call for each match. There is no
   *     default callback.
   *   - 'recurse': When TRUE, the directory scan will recurse the entire tree
   *     starting at the provided directory. Defaults to TRUE.
   *   - 'key': The key to be used for the returned associative array of files.
   *     Possible values are 'uri', for the file's URI; 'filename', for the
   *     basename of the file; and 'name' for the name of the file without the
   *     extension. Defaults to 'uri'.
   *   - 'min_depth': Minimum depth of directories to return files from.
   *     Defaults to 0.
   * @param int $depth
   *   Current depth of recursion. This parameter is only used internally and
   *   should not be passed in.
   *
   * @return array
   *   An associative array (keyed on the chosen key) of objects with 'uri',
   *   'filename', and 'name' members corresponding to the matching files.
   */
  protected function scanDirectory($dir, $mask, array $options = [], $depth = 0) {
    // Merge in defaults.
    $options += [
      'nomask' => '/(\.\.?|CVS)$/',
      'nodirmask' => '/(\.git)/',
      'callback' => 0,
      'recurse' => TRUE,
      'key' => 'uri',
      'min_depth' => 0,
    ];

    $options['key'] = in_array($options['key'], ['uri', 'filename', 'name']) ? $options['key'] : 'uri';
    $files = [];

    if (is_dir($dir)) {
      $handle = opendir($dir);
      if ($handle) {
        while (FALSE !== ($filename = readdir($handle))) {
          // Skip if filename matches the nomask or is '.'.
          if (preg_match($options['nomask'], $filename) || $filename[0] === '.') {
            continue;
          }

          $uri = "$dir/$filename";
          $uri = $this->streamWrapperManager->normalizeUri($uri);
          if (is_dir($uri) && $options['recurse'] && !preg_match($options['nodirmask'], $uri)) {
            // Give priority to files in this folder by merging them in after
            // any subdirectory files.
            $files = array_merge($this->scanDirectory($uri, $mask, $options, $depth + 1), $files);
          }
          elseif ($depth >= $options['min_depth'] && preg_match($mask, $filename)) {
            // Always use this match over anything already set in $files with
            // the same $$options['key'].
            $file = new \stdClass();
            $file->uri = $uri;
            $file->filename = $filename;
            $file->name = pathinfo($filename, PATHINFO_FILENAME);
            $key = $options['key'];
            $files[$file->$key] = $file;
            if ($options['callback']) {
              $options['callback']($uri);
            }
          }
        }

        closedir($handle);
      }
    }

    return $files;
  }

  /**
   * Perform server side test(s) on all given files.
   *
   * @param array $files
   *   An array of files to be tested.
   * @param array $options
   *   (optional) An array of options to use in the test.
   *
   * @return array
   *   An array of files with the result.
   */
  protected function testFiles(array $files, array $options = []) {
    return $files;
  }

  /**
   * Extract info from the DOMNode Object.
   *
   * @param object $dom
   *   DOMNode Class.
   *
   * @return array
   *   Key Value pair from the DOM Node.
   */
  protected function domExtractor($dom) {
    $node = $dom->firstChild;
    $output = [];
    do {
      $text = trim($node->nodeValue);
      if (!empty($text)) {
        $key = str_replace('m:', '', $node->nodeName);
        $output[$key] = $text;
      }
    } while ($node = $node->nextSibling);
    return $output;
  }

  /**
   * Get array element that corresponds to directory.
   *
   * @param array $array
   *   An associative array to check for the key. Usually a form array.
   * @param array $keys_array
   *   An array of keys to check sequentially in a heirachical manner.
   *
   * @return array|bool|mixed
   *   The array element or FALSE if not found.
   */
  protected function getElement(array $array, array $keys_array) {
    foreach ($keys_array as $key) {
      if (!isset($key, $array)) {
        return FALSE;
      }
      $array = $array[$key];
    }
    return $array;
  }

}
