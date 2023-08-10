<?php

namespace Drupal\Tests\advagg_ext_minify\Kernel\Form;

use Drupal\Core\Form\FormInterface;
use Drupal\KernelTests\KernelTestBase;
use Drupal\advagg_ext_minify\Form\SettingsForm;

/**
 * Tests the External Minification settings form.
 *
 * @group advagg
 */
class SettingsFormTest extends KernelTestBase {

  /**
   * The External Minification settings form object under test.
   *
   * @var \Drupal\advagg_ext_minify\Form\SettingsForm
   */
  protected $settingsForm;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'system',
    'advagg',
    'advagg_ext_minify',
  ];

  /**
   * {@inheritdoc}
   *
   * @covers ::__construct
   */
  protected function setUp() {
    parent::setUp();

    $this->installConfig(static::$modules);

    $this->settingsForm = SettingsForm::create($this->container);
  }

  /**
   * Tests for \Drupal\advagg_ext_minify\Form\SettingsForm.
   */
  public function testSettingsForm() {

    $this->assertInstanceOf(FormInterface::class, $this->settingsForm);

    $id = $this->settingsForm->getFormId();
    $this->assertEquals('advagg_ext_minify_settings', $id);

    $method = new \ReflectionMethod(SettingsForm::class, 'getEditableConfigNames');
    $method->setAccessible(TRUE);

    $name = $method->invoke($this->settingsForm);
    $this->assertEquals(['advagg_ext_minify.settings'], $name);
  }

}
