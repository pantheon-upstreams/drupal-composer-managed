<?php

namespace Drupal\Tests\advagg_bundler\Kernel\Form;

use Drupal\Core\Form\FormInterface;
use Drupal\KernelTests\KernelTestBase;
use Drupal\advagg_bundler\Form\SettingsForm;

/**
 * Tests the Bundler settings form.
 *
 * @group advagg
 */
class SettingsFormTest extends KernelTestBase {

  /**
   * The Bundler settings form object under test.
   *
   * @var \Drupal\advagg_bundler\Form\SettingsForm
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
    'advagg_bundler',
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
   * Tests for \Drupal\advagg_bundler\Form\SettingsForm.
   */
  public function testSettingsForm() {

    $this->assertInstanceOf(FormInterface::class, $this->settingsForm);

    $id = $this->settingsForm->getFormId();
    $this->assertEquals('advagg_bundler_settings', $id);

    $method = new \ReflectionMethod(SettingsForm::class, 'getEditableConfigNames');
    $method->setAccessible(TRUE);

    $name = $method->invoke($this->settingsForm);
    $this->assertEquals(['advagg_bundler.settings'], $name);
  }

}
