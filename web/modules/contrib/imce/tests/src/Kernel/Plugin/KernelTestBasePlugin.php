<?php

namespace Drupal\Tests\imce\Kernel\Plugin;

use Drupal\Core\StreamWrapper\PublicStream;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\imce\Imce;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\user\Traits\UserCreationTrait;
use Drupal\text\Plugin\Field\FieldWidget\TextareaWithSummaryWidget;

/**
 * The abstract class base to imce kernel tests.
 */
abstract class KernelTestBasePlugin extends KernelTestBase {

  use StringTranslationTrait;
  use UserCreationTrait;

  /**
   * The Imce file manager.
   *
   * @var \Drupal\imce\ImceFM
   */
  public $imceFM;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'user',
    'config',
    'file',
    'system',
    'imce',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() : void {
    parent::setUp();
    $this->installSchema('system', ['sequences']);
    $this->installConfig(static::$modules);
    $this->installEntitySchema('user');
    $this->installEntitySchema('file');
    $this->installSchema('file', ['file_usage']);

    $this->setUpCurrentUser(['uid' => 1], [
      'access user profiles', 'administer imce', 'access files overview',
    ], TRUE);
  }

  /**
   * The Imce file manager.
   *
   * @return \Drupal\imce\ImceFM
   *   Return the file manager.
   */
  public function getImceFm() {
    $imceFM = Imce::userFM(
      $this->container->get('current_user'), NULL, $this->getRequest()
    );
    $imceFM->setConf("root_uri", "public://");
    $imceFM->setConf("root_url", "/sites/default/files");
    return $imceFM;
  }

  /**
   * Get the request parameter.
   *
   * @return \Symfony\Component\HttpFoundation\Request
   *   The request object.
   */
  abstract public function getRequest();

  /**
   * The get conf.
   *
   * @return array
   *   Return array conf.
   */
  public function getConf() {
    return [
      "extensions" => "*",
      "maxsize" => '104857600.0',
      "quota" => 0,
      "maxwidth" => 0,
      "maxheight" => 0,
      "replace" => 0,
      "thumbnail_style" => "",
      "folders" => [
        "." => [
          "permissions" => [
            "all" => TRUE,
          ],
        ],
      ],
      "pid" => "admin",
      "scheme" => "public",
      "root_uri" => "public://",
      "root_url" => "/sites/default/files",
      "token" => "Vof6182Y9jbV1jFfCU0arR2XDI8qs-OfO8c-R-IbkTg",
    ];
  }

  /**
   * Get plugins definations.
   *
   * @return array
   *   Return plugins definations.
   */
  public function getPluginDefinations() {
    return [
      "field_types" => [
        0 => "text_with_summary",
      ],
      "multiple_values" => FALSE,
      "id" => "text_textarea_with_summary",
      "label" => $this->t("Text area with a summary"),
      "class" => TextareaWithSummaryWidget::class,
      "provider" => "text",
    ];
  }

  /**
   * Gets test image file.
   *
   * @return string
   *   uri.
   */
  protected function getTestFileUri() {
    \Drupal::service('file_system')->copy(\Drupal::service('extension.list.module')->getPath('imce') . '/tests/files/ciandt.jpg', PublicStream::basePath());
    return 'public://ciandt.jpg';
  }

}
