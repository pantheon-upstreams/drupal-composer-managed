<?php

namespace Drupal\surf_core\Twig\Extension;

use Drupal\Core\Render\Element;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Twig extension with some useful functions and filters.
 *
 * The extension consumes quite a lot of dependencies. Most of them are not used
 * on each page request. For performance reasons services are wrapped in static
 * callbacks.
 */
class SurfTwigExtension extends AbstractExtension {
  /**
   * @return \Twig\TwigFilter[]
   */
  public function getFilters() {
    return [
      new TwigFilter('addModifiers', [$this, 'addModifiers']),
      new TwigFilter('field_empty', [$this, 'getFieldEmpty']),
      new TwigFilter('mercury_editor', [$this, 'getMercuryEditor']),
    ];
  }

  public function addModifiers($baseClass, $modifiers = [], $separator = '--') {
    $result = [$baseClass];

    if (!empty($modifiers) && !is_array($modifiers)) {
      $modifiers = [$modifiers];
    }

    foreach ($modifiers as $modifier) {
      $result[] = $baseClass . $separator . $modifier;
    }

    return $result;
  }

  public function getMercuryEditor($build) {
    $routeName = \Drupal::routeMatch()->getRouteName();

    $mercuryEditorRoutes = [
      'mercury_editor.preview',
      'mercury_editor.builder.choose_component',
      'mercury_editor.builder.insert',
    ];

    if (in_array($routeName, $mercuryEditorRoutes)) {
      return $build;
    }
  }

  /**
   * Twig filter callback: Only return a field's value(s).
   *
   * @param array|null $build
   *   Render array of a field.
   *
   * @return array
   *   Array of render array(s) of field value(s). If $build is not the render
   *   array of a field, NULL is returned.
   */
  public function getFieldEmpty($build) {
    if ($this->isLayoutParagraphs($build)) {
      return FALSE;
    }

    if (!$this->isFieldRenderArray($build)) {
      return TRUE;
    }

    return empty($this->getVisibleChildren($build));
  }

  /**
   * Checks whether the render array is setting up Layout Paragraphs.
   *
   * @param array|null $build
   *   The render array.
   *
   * @return bool
   *   True if $build is a LP render array.
   */
  protected function isLayoutParagraphs($build) {
    return isset($build['#type']) && $build['#type'] == 'layout_paragraphs_builder';
  }

  /**
   * Checks whether the render array is a field's render array.
   *
   * @param array|null $build
   *   The render array.
   *
   * @return bool
   *   True if $build is a field render array.
   */
  protected function isFieldRenderArray($build) {
    return isset($build['#theme']) && $build['#theme'] == 'field';
  }

  /**
   * Returns the children that are accessible.
   *
   * @param array $build
   *   Render array.
   *
   * @return array
   *   Visible children.
   */
  protected function getVisibleChildren(array $build) {
    $elements = Element::children($build);
    if (empty($elements)) {
      return [];
    }

    $children = [];
    foreach ($elements as $delta) {
      if (Element::isVisibleElement($build[$delta])) {
        $children[$delta] = $build[$delta];
      }
    }

    return $children;
  }
}
