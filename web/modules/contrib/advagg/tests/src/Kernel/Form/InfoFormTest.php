<?php

namespace Drupal\Tests\advagg\Kernel\Form;

use Drupal\Core\Form\FormInterface;
use Drupal\KernelTests\KernelTestBase;
use Drupal\advagg\Form\InfoForm;

/**
 * Tests the Info settings form.
 *
 * @group advagg
 */
class InfoFormTest extends KernelTestBase {

  /**
   * The Info form object under test.
   *
   * @var \Drupal\advagg\Form\InfoForm
   */
  protected $infoForm;

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

    $this->infoForm = InfoForm::create($this->container);
  }

  /**
   * Tests for \Drupal\advagg\Form\InfoForm.
   */
  public function testInfoForm() {

    $this->assertInstanceOf(FormInterface::class, $this->infoForm);

    $id = $this->infoForm->getFormId();
    $this->assertEquals('advagg_info', $id);

    $method = new \ReflectionMethod(InfoForm::class, 'getEditableConfigNames');
    $method->setAccessible(TRUE);

    $name = $method->invoke($this->infoForm);
    $this->assertEquals([], $name);
  }

}
