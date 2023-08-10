<?php

namespace Drupal\Tests\advagg\Functional;

use Drupal\Core\Url;

/**
 * Test combining css media queries.
 *
 * @ingroup advagg_tests
 *
 * @group advagg
 */
class CombineMediaQueriesTest extends AdvaggFunctionalTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['advagg', 'advagg_test'];

  /**
   * Tests path converting functions, and that saving a change to them works.
   */
  public function testCombineMedia() {
    // Agreggrate files.
    $this->config('system.performance')->set('css.preprocess', TRUE)->save();

    // Ensure that when combine media is disabled, that there is a media query.
    $this->drupalGet(Url::fromRoute('advagg.settings'));
    $this->assertSession()->responseContains('media="screen"');

    // Test combine media.
    $edit = [];
    $edit['css_combine_media'] = TRUE;
    $this->submitForm($edit, 'op');

    $config = $this->config('advagg.settings');
    $this->assertTrue($config->get('css.combine_media'));
    $this->assertSession()->responseNotContains('media="screen"');

    // Ensure that if the optimization is cached that the asset definition
    // is still updated.
    $this->drupalGet(Url::fromRoute('advagg.operations'));
    $this->assertSession()->responseNotContains('media="screen"');
  }

}
