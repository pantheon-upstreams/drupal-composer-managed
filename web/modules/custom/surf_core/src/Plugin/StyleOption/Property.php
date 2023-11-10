<?php

declare(strict_types=1);

namespace Drupal\surf_core\Plugin\StyleOption;

use Drupal\Core\Form\FormStateInterface;
use Drupal\style_options\Plugin\StyleOptionPluginBase;

/**
 * Define the class attribute option plugin.
 *
 * @StyleOption(
 *   id = "property",
 *   label = @Translation("Property")
 * )
 */
class Property extends StyleOptionPluginBase {

  /**
   * {@inheritDoc}
   */
  public function buildConfigurationForm(
    array $form,
    FormStateInterface $form_state): array {

    $form['property'] = [
      '#type' => 'textfield',
      '#title' => $this->getLabel(),
      '#default_value' => $this->getValue('property') ?? $this->getDefaultValue(),
      '#wrapper_attributes' => [
        'class' => [$this->getConfiguration()['property'] ?? ''],
      ],
      '#description' => $this->getConfiguration('description'),
    ];

    if ($this->hasConfiguration('options')) {
      $form['property']['#type'] = 'select';
      $options = $this->getConfiguration()['options'];

      if (
        class_exists('\Drupal\image_radios\Element\ImageRadios') &&
        count(array_filter($options, function ($option) {
          return isset($option['image']);
        }))) {

        $form['property']['#type'] = 'image_radios';
      }
      else {
        array_walk($options, function (&$option) {
          $option = $option['label'];
        });
        if ($this->hasConfiguration('multiple')) {
          $form['property']['#multiple'] = TRUE;
        }
      }

      $form['property']['#options'] = $options;
    }

    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function build(array $build) {
    $value = $this->getValue('property') ?? NULL;
    $option_definition = $this->getConfiguration('options');
    if (is_array($value)) {
      $property = implode(' ',
        array_map(function ($index) use ($option_definition) {
          return $option_definition[$index]['value'] ?? NULL;
        }, $value)
      );
    }
    else {
      $property = $value ?? NULL;
    }
    if (!empty($property)) {
      $build['#' . $this->getOptionId()] = $property;
    }
    return $build;
  }

}
