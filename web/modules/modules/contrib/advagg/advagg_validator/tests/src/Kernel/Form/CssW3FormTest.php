<?php

namespace Drupal\Tests\advagg_validator\Kernel\Form;

use Drupal\Core\Form\FormInterface;
use Drupal\KernelTests\KernelTestBase;
use Drupal\advagg_validator\Form\CssW3Form;

/**
 * Tests the validator css W3  settings form.
 *
 * @group advagg
 */
class CssW3FormTest extends KernelTestBase {

  /**
   * The validator css W3 settings form object under test.
   *
   * @var \Drupal\advagg_validator\Form\CssW3Form
   */
  protected $cssW3Form;

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

    $this->cssW3Form = CssW3Form::create($this->container);
  }

  /**
   * Tests for \Drupal\advagg_validator\Form\CssW3Form.
   */
  public function testSettingsForm() {

    $this->assertInstanceOf(FormInterface::class, $this->cssW3Form);

    $id = $this->cssW3Form->getFormId();
    $this->assertEquals('advagg_validator_cssw3', $id);

    $method = new \ReflectionMethod(CssW3Form::class, 'getEditableConfigNames');
    $method->setAccessible(TRUE);

    $name = $method->invoke($this->cssW3Form);
    $this->assertEquals(['advagg_validator.settings'], $name);
  }

}
