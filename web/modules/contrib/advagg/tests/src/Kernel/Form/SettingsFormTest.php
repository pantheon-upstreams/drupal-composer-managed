<?php

namespace Drupal\Tests\advagg\Kernel\Form;

use Drupal\advagg\Form\SettingsForm;
use Drupal\Core\Form\FormInterface;
use Drupal\KernelTests\KernelTestBase;

/**
 * Class for test Drupal\advagg\Form\SettingsForm.
 *
 * @group advagg
 */
class SettingsFormTest extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'advagg',
  ];

  /**
   * The Advagg settingForm.
   *
   * @var \Drupal\advagg\Form\SettingsForm
   */
  protected $settingsForm;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->settingsForm = SettingsForm::create($this->container);

  }

  /**
   * Test the cache level options.
   */
  public function testCacheLevelOption() {
    $options = $this->settingsForm->getCacheLevelOptions();
    $this->assertIsArray($options);
    $this->assertTrue(in_array('Development', $options));
    $this->assertTrue(in_array('High', $options));
  }

  /**
   * Test the method getShortTimes.
   */
  public function testShortTimes() {
    $shortTime = $this->settingsForm->getShortTimes();
    $this->assertIsArray($shortTime);
    $this->assertTrue(in_array('15 minutes', $shortTime));
    $this->assertTrue(in_array('2 days', $shortTime));
  }

  /**
   * Test the method getLongTimes.
   */
  public function testLongTimes() {
    $longTimes = $this->settingsForm->getLongTimes();
    $this->assertIsArray($longTimes);
    $this->assertTrue(in_array('2 days', $longTimes));
    $this->assertTrue(in_array('2 months', $longTimes));
  }

  /**
   * Tests for \Drupal\advagg\Form\SettingsForm.
   */
  public function testSettingsForm() {
    $this->assertInstanceOf(FormInterface::class, $this->settingsForm);

    $id = $this->settingsForm->getFormId();
    $this->assertEquals('advagg_settings', $id);

    $method = new \ReflectionMethod(SettingsForm::class, 'getEditableConfigNames');
    $method->setAccessible(TRUE);

    $name = $method->invoke($this->settingsForm);
    $this->assertEquals(['advagg.settings', 'system.performance'], $name);
  }

}
