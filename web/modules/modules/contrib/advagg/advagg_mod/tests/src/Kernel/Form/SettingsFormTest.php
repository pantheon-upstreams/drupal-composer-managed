<?php

namespace Drupal\Tests\advagg_mod\Kernel\Form;

use Drupal\Core\Form\FormInterface;
use Drupal\KernelTests\KernelTestBase;
use Drupal\advagg_mod\Form\SettingsForm;

/**
 * Tests the Modifier Minification settings form.
 *
 * @group advagg
 */
class SettingsFormTest extends KernelTestBase {

  /**
   * The Modifier Minification settings form object under test.
   *
   * @var \Drupal\advagg_mod\Form\SettingsForm
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
    'advagg_mod',
  ];

  /**
   * {@inheritdoc}
   *
   * @covers ::__construct
   */
  protected function setUp() {
    parent::setUp();

    $this->installConfig(static::$modules);

    $this->settingsForm = new SettingsForm(
      $this->container->get('config.factory'),
      $this->container->get('cache.advagg'),
      $this->container->get('module_handler'),
      $this->container->get('language_manager')
    );
  }

  /**
   * Tests for \Drupal\advagg_mod\Form\SettingsForm.
   */
  public function testSettingsForm() {

    $this->assertInstanceOf(FormInterface::class, $this->settingsForm);

    $id = $this->settingsForm->getFormId();
    $this->assertEquals('advagg_mod_settings', $id);

    $method = new \ReflectionMethod(SettingsForm::class, 'getEditableConfigNames');
    $method->setAccessible(TRUE);

    $name = $method->invoke($this->settingsForm);
    $this->assertEquals(['advagg_mod.settings'], $name);
  }

}
