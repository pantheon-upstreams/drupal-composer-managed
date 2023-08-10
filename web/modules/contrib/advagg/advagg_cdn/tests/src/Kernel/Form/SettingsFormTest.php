<?php

namespace Drupal\Tests\advagg_cdn\Kernel\Form;

use Drupal\Core\Form\FormInterface;
use Drupal\KernelTests\KernelTestBase;
use Drupal\advagg_cdn\Form\SettingsForm;

/**
 * Tests the CDN settings form.
 *
 * @group advagg
 */
class SettingsFormTest extends KernelTestBase {

  /**
   * The CDN settings form object under test.
   *
   * @var \Drupal\advagg_cdn\Form\SettingsForm
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
    'advagg_cdn',
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
   * Tests for \Drupal\advagg_cdn\Form\SettingsForm.
   */
  public function testSettingsForm() {

    $this->assertInstanceOf(FormInterface::class, $this->settingsForm);

    $id = $this->settingsForm->getFormId();
    $this->assertEquals('advagg_cdn_settings', $id);

    $method = new \ReflectionMethod(SettingsForm::class, 'getEditableConfigNames');
    $method->setAccessible(TRUE);

    $name = $method->invoke($this->settingsForm);
    $this->assertEquals(['advagg_cdn.settings'], $name);
  }

}
