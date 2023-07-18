<?php

namespace Drupal\Tests\imce\Kernel\Form;

use Drupal\Core\Form\FormInterface;
use Drupal\imce\Form\ImceSettingsForm;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Kernel tests for ImceSettingsForm.
 *
 * @group imce
 */
class ImceSettingsFormTest extends KernelTestBase {

  use StringTranslationTrait;

  /**
   * The IMCE sttings form.
   *
   * @var \Drupal\imce\Form\ImceSettingsForm
   */
  protected $imceSettingsForm;

  /**
   * The IMCE settings.
   *
   * @var object
   */
  protected $imceConfig;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'system',
    'user',
    'imce',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() : void {
    parent::setUp();
    $this->imceConfig = $this->container->get('config.factory')->get('imce.settings');
    $this->imceSettingsForm = ImceSettingsForm::create($this->container);
  }

  /**
   * Test formId().
   */
  public function testFormId() {
    $this->assertIsString($this->imceSettingsForm->getFormId());
    $this->assertEquals('imce_settings_form', $this->imceSettingsForm->getFormId());
  }

  /**
   * Test method getProfileOptions().
   */
  public function testProfileOptions() {
    $options = $this->imceSettingsForm->getProfileOptions();
    $this->assertIsArray($options);
    $this->assertTrue(in_array('-' . $this->t('None') . '-', $options));
  }

  /**
   * Test method buildHeaderProfilesTable().
   */
  public function testBuildHeaderProfilesTable() {
    $headerProfiles = $this->imceSettingsForm->buildHeaderProfilesTable();
    $this->assertIsArray($headerProfiles);
  }

  /**
   * Test method buildRolesProfilesTable().
   */
  public function testBuildRolesProfilesTable() {
    $this->assertIsArray(
      $this->imceSettingsForm->buildRolesProfilesTable($this->imceConfig->get('roles_profiles') ?: [])
    );
  }

  /**
   * Test editable config name.
   */
  public function testEditableConfigName() {
    $method = new \ReflectionMethod(ImceSettingsForm::class, 'getEditableConfigNames');
    $method->setAccessible(TRUE);

    $configName = $method->invoke($this->imceSettingsForm);
    $this->assertEquals(['imce.settings'], $configName);
  }

  /**
   * Test imce settings form.
   */
  public function testImceSettingsForm() {
    $this->assertInstanceOf(FormInterface::class, $this->imceSettingsForm);
  }

}
