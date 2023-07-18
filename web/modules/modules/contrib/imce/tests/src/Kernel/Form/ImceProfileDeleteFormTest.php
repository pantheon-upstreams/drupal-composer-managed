<?php

namespace Drupal\Tests\imce\Kernel\Form;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\imce\Form\ImceProfileDeleteForm;

/**
 * Kernel tests for ImceProfileDeleteForm.
 *
 * @group imce
 */
class ImceProfileDeleteFormTest extends KernelTestBase {

  use StringTranslationTrait;

  /**
   * The form delete profile.
   *
   * @var \Drupal\imce\Form\ImceProfileDeleteForm
   */
  protected $profileDeleteForm;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'imce',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() : void {
    parent::setUp();
    $this->profileDeleteForm = new ImceProfileDeleteForm();
  }

  /**
   * Test the method getCancelUrl().
   */
  public function testCancelUrl() {
    $url = $this->profileDeleteForm->getCancelUrl();
    $this->assertInstanceOf(Url::class, $url);
    $this->assertIsString($url->toString());
    $this->assertSame('/admin/config/media/imce', $url->toString());
    $this->assertEquals('/admin/config/media/imce', $url->toString());
  }

  /**
   * Test the method getConfirmText().
   */
  public function testConfirmText() {
    $confirmText = $this->profileDeleteForm->getConfirmText();
    $this->assertInstanceOf(TranslatableMarkup::class, $confirmText);
    $this->assertIsString($confirmText->__toString());
    $this->assertEquals($this->t('Delete'), $confirmText);
  }

}
