<?php

namespace Drupal\Tests\advagg_validator\Kernel\Form;

use Drupal\Core\Form\FormInterface;
use Drupal\KernelTests\KernelTestBase;
use Drupal\advagg_validator\Form\JsHintForm;

/**
 * Tests the validator JsHint settings form.
 *
 * @group advagg
 */
class JsHintFormTest extends KernelTestBase {

  /**
   * The validator JsHint settings form object under test.
   *
   * @var \Drupal\advagg_validator\Form\JsHintForm
   */
  protected $jsHintForm;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [];

  /**
   * {@inheritdoc}
   *
   * @covers ::__construct
   */
  protected function setUp() {
    parent::setUp();

    $this->installConfig(static::$modules);

    $this->jsHintForm = JsHintForm::create($this->container);
  }

  /**
   * Tests for \Drupal\advagg_validator\Form\JsHintForm.
   */
  public function testSettingsForm() {

    $this->assertInstanceOf(FormInterface::class, $this->jsHintForm);

    $id = $this->jsHintForm->getFormId();
    $this->assertEquals('advagg_validator_jshint', $id);

    $method = new \ReflectionMethod(JsHintForm::class, 'getEditableConfigNames');
    $method->setAccessible(TRUE);

    $name = $method->invoke($this->jsHintForm);
    $this->assertEquals(['advagg_validator.settings'], $name);
  }

}
