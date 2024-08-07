<?php

namespace Drupal\Tests\advagg\Kernel\Form;

use Drupal\Core\Form\FormInterface;
use Drupal\KernelTests\KernelTestBase;
use Drupal\advagg\Form\OperationsForm;

/**
 * Tests the Operations settings form.
 *
 * @group advagg
 */
class OperationsFormTest extends KernelTestBase {

  /**
   * The Operations form object under test.
   *
   * @var \Drupal\advagg\Form\OperationsForm
   */
  protected $operationsForm;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'system',
    'advagg',
  ];

  /**
   * {@inheritdoc}
   *
   * @covers ::__construct
   */
  protected function setUp() {
    parent::setUp();

    $this->installConfig(static::$modules);

    $this->operationsForm = OperationsForm::create($this->container);
  }

  /**
   * Tests for \Drupal\advagg\Form\OperationsForm.
   */
  public function testOperationsForm() {

    $this->assertInstanceOf(FormInterface::class, $this->operationsForm);

    $id = $this->operationsForm->getFormId();
    $this->assertEquals('advagg_operations', $id);

    $method = new \ReflectionMethod(OperationsForm::class, 'getEditableConfigNames');
    $method->setAccessible(TRUE);

    $name = $method->invoke($this->operationsForm);
    $this->assertEquals(['advagg.settings'], $name);
  }

}
