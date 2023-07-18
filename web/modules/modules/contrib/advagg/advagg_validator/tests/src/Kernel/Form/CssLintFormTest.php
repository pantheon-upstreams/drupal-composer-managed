<?php

namespace Drupal\Tests\advagg_validator\Kernel\Form;

use Drupal\Core\Form\FormInterface;
use Drupal\KernelTests\KernelTestBase;
use Drupal\advagg_validator\Form\CssLintForm;

/**
 * Tests the Modifier Minification settings form.
 *
 * @group advagg
 */
class CssLintFormTest extends KernelTestBase {

  /**
   * The Modifier Minification settings form object under test.
   *
   * @var \Drupal\advagg_validator\Form\CssLintForm
   */
  protected $cssLintForm;

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

    $this->cssLintForm = CssLintForm::create($this->container);
  }

  /**
   * Tests for \Drupal\advagg_validator\Form\CssLintForm.
   */
  public function testSettingsForm() {

    $this->assertInstanceOf(FormInterface::class, $this->cssLintForm);

    $id = $this->cssLintForm->getFormId();
    $this->assertEquals('advagg_validator_csslint', $id);

    $method = new \ReflectionMethod(CssLintForm::class, 'getEditableConfigNames');
    $method->setAccessible(TRUE);

    $name = $method->invoke($this->cssLintForm);
    $this->assertEquals(['advagg_validator.settings'], $name);
  }

}
