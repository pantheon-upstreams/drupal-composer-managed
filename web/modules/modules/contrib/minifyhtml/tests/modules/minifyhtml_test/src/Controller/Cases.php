<?php

namespace Drupal\minifyhtml_test\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\HtmlResponse;

/**
 * Provides test cases for minifyhtml module.
 *
 * @package Drupal\minifyhtml_test\Controller
 */
class Cases extends ControllerBase {

  /**
   * Collection of endpoints with test cases.
   *
   * See dataProvider for expected results.
   *
   * @param string $case
   *   Test case key.
   *
   * @return \Drupal\Core\Render\HtmlResponse
   *   Test response.
   *
   * @see \Drupal\Tests\minifyhtml\Functional\ResponseTest::dataProvider()
   */
  public function item($case) {
    $input = '';
    switch ($case) {
      case 'textarea_replacement':
        // Test Minify HTML Textarea Replacement.
        $input .= <<<EOT
<html lang="xx">
  <head>
    <title>Test HTML</title>
  </head>
  <body>
    <textarea cols="55" rows="31">
Content in here will not matter.
Even multiline content.
</textarea>
  </body>
</html>
EOT;
        break;

      case 'pre_replacement':
        // Test Minify HTML Pre Replacement.
        $input .= <<<EOT
<html lang="xx">
  <head>
    <title>Test HTML</title>
  </head>
  <body>
    <pre>
  Indented content.
         Weirdly Indented content.
Non-indented content.
</pre>
  </body>
</html>
EOT;
        break;

      case 'iframe_replacement':
        // Test Minify HTML Iframe Replacement.
        $input .= <<<EOT
<html lang="xx">
  <head>
    <title>Test HTML</title>
  </head>
  <body>
    <iframe src="" width="100" height="100" ></iframe>
  </body>
</html>
EOT;
        break;

      case 'script_replacement':
        // Test Minify HTML Script Replacement.
        $input .= <<<EOT
<html lang="xx">
  <head>
    <title>Test HTML</title>
  </head>
  <body>
    <script>
      alert('test');
    </script>
  </body>
</html>
EOT;
        break;

      case 'style_replacement':
        // Test Minify HTML Style Replacement.
        $input .= <<<EOT
<html lang="xx">
  <head>
    <title>Test HTML</title>
  </head>
  <body>
    <style>
      body { color: #fff; }
    </style>
  </body>
</html>
EOT;
        break;

      case 'comment_stripping':
        // Test Minify HTML Comment Stripping.
        $input .= <<<EOT
<html lang="xx">
  <head>
    <title>Test HTML</title>
  </head>
  <body>
    <!-- The body goes here //-->
  </body>
</html>
EOT;
        break;

      case 'correct_iframe_script_stripping_order':
        // Test Correct Iframe and Script stripping order.
        $input .= <<<EOT
<html lang="xx">
  <head>
    <title>Test HTML</title>
  </head>
  <body>
    <script type="text/javascript">
      let axel = Math.random() + "";
      let a = axel * 10000000000000;
      document.write('<iframe src=""></iframe>');
    </script>
  </body>
</html>
EOT;
        break;

      default:
    }

    return HtmlResponse::create($input);
  }

}
