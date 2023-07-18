<?php

namespace Drupal\Tests\minifyhtml\Functional;

use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests the response to ensure HTML is minified.
 *
 * @package Drupal\Tests\minifyhtml\Kernel
 *
 * @group minifyhtml
 */
class ResponseTest extends BrowserTestBase {

  /**
   * The theme to install as the default for testing.
   *
   * Defaults to the install profile's default theme, if it specifies any.
   *
   * @var string
   */
  protected $defaultTheme = 'stark';

  /**
   * Modules to enable.
   *
   * The test runner will merge the $modules lists from this class, the class
   * it extends, and so on up the class hierarchy. It is not necessary to
   * include modules in your list that a parent class has already declared.
   *
   * @var string[]
   *
   * @see \Drupal\Tests\BrowserTestBase::installDrupal()
   */
  protected static $modules = ['minifyhtml_test'];

  /**
   * Set of cases with expected results.
   *
   * See Cases controller for test input data.
   *
   * @return array
   *   Test cases.
   *
   * @see \Drupal\minifyhtml_test\Controller\Cases::item()
   */
  public function dataProvider() {
    $data = [];

    // Test Minify HTML Textarea Replacement.
    $data['textarea_replacement'] = [
      'textarea_replacement',
      <<<EOT
<html lang="xx"><head><title>Test HTML</title></head><body><textarea cols="55" rows="31">
Content in here will not matter.
Even multiline content.
</textarea></body></html>
EOT,
    ];

    // Test Minify HTML Pre Replacement.
    $data['pre_replacement'] = [
      'pre_replacement',
      <<<EOT
<html lang="xx"><head><title>Test HTML</title></head><body><pre>
  Indented content.
         Weirdly Indented content.
Non-indented content.
</pre></body></html>
EOT,
    ];

    // Test Minify HTML Iframe Replacement.
    $data['iframe_replacement'] = [
      'iframe_replacement',
      '<html lang="xx"><head><title>Test HTML</title></head><body><iframe src="" width="100" height="100" ></iframe></body></html>',
    ];

    // Test Minify HTML Script Replacement.
    $data['script_replacement'] = [
      'script_replacement',
      <<<EOT
<html lang="xx"><head><title>Test HTML</title></head><body><script>
alert('test');
</script></body></html>
EOT,
    ];

    // Test Minify HTML Style Replacement.
    $data['style_replacement'] = [
      'style_replacement',
      <<<EOT
<html lang="xx"><head><title>Test HTML</title></head><body><style>
body { color: #fff; }
</style></body></html>
EOT,
    ];

    // Test Minify HTML Comment Stripping.
    $data['comment_stripping'] = [
      'comment_stripping',
      '<html lang="xx"><head><title>Test HTML</title></head><body></body></html>',
    ];

    // Test Correct Iframe and Script stripping order.
    $data['correct_iframe_script_stripping_order'] = [
      'correct_iframe_script_stripping_order',
      <<<EOT
<html lang="xx"><head><title>Test HTML</title></head><body><script type="text/javascript">
let axel = Math.random() + "";
let a = axel * 10000000000000;
document.write('<iframe src=""></iframe>');
</script></body></html>
EOT,
    ];

    return $data;
  }

  /**
   * Test possible cases which covered by the module.
   *
   * @param string $case
   *   Case which currently tested. Passed to the route parameters to reach
   *   controller with required response.
   * @param string $expected_output
   *   Expected response result.
   *
   * @dataProvider dataProvider
   */
  public function testMinifyHtml($case, $expected_output) {
    $this->drupalGet(Url::fromRoute('minifyhtml_test.case', ['case' => $case]));
    $actual_output = $this->getSession()->getPage()->getContent();
    $this->assertEquals($expected_output, $actual_output, 'Minified source not matches expected output.');
    $this->assertSame($expected_output, $actual_output, 'Minified source not matches expected output.');
  }

}
