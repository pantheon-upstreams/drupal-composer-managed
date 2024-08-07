<?php

namespace Drupal\Tests\simple_gmap\Kernel;

use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;

use Drupal\node\Entity\Node;
use Drupal\Core\Render\Markup;

/**
 * Tests the text formatters functionality.
 *
 * @group text
 */
class SimpleGMapFormatterTest extends EntityKernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['node', 'simple_gmap', 'simple_gmap_stress_test'];

  /**
   * A instance of the simple gmap stress test content type.
   *
   * @var \Drupal\Core\Entity\EntityInterface
   */
  protected $node;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installConfig('node');
    $this->installEntitySchema('node');
    $this->installConfig('simple_gmap');
    $this->installConfig('simple_gmap_stress_test');

    // Populate the node this the default values.
    $this->node = Node::create([
      'type' => 'simple_gmap_stress_test',
      'title' => 'Stress ball',
    ]);
    $this->node->save();

  }

  /**
   * Inspect the formatter output.
   *
   * Troublesome senarious :-
   *   A complex character set.
   *   A XSS attack.
   */
  public function testFormatterOutput() {

    $viewBuilder = \Drupal::entityTypeManager()->getViewBuilder('node');
    $renderer = \Drupal::service('renderer');

    $values = [
      'field_map2' => 'Place de l&amp;#039;Université-du-Québec, boulevard Charest Est, Québec, QC G1K',
      'field_xss' => '&lt;script&gt;alert(&quot;hello&quot;);&lt;/script&gt; Empire State Building',
    ];

    foreach ($values as $field => $raw_text) {
      $view = $this->node->get($field)->view();
      $renderer->renderRoot($view[0]);

      $expected_markup = Markup::create($raw_text);
      $this->assertEquals($view[0]['#children'], $expected_markup);
    }
  }

}
