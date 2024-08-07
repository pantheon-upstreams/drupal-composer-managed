<?php

namespace Drupal\Tests\imce\Kernel\Plugin\ImcePlugin;

use Drupal\Core\StreamWrapper\PublicStream;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\imce\ImceFolder;
use Drupal\imce\ImcePluginInterface;
use Drupal\imce\Plugin\ImcePlugin\Resize;
use Drupal\Tests\imce\Kernel\Plugin\KernelTestBasePlugin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Kernel tests for Imce plugins for Imce Plugin Core.
 *
 * @group imce
 */
class ResizeTest extends KernelTestBasePlugin {

  use StringTranslationTrait;

  /**
   * The Imce ckeditor plugin.
   *
   * @var \Drupal\imce\Plugin\ImcePlugin\Resize
   */
  public $resize;

  /**
   * The Imce file manager.
   *
   * @var \Drupal\imce\ImceFM
   */
  public $imceFM;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'user',
    'config',
    'file',
    'system',
    'imce',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() : void {
    parent::setUp();
    $this->imceFM = $this->getImceFM();
    $this->getTestFileUri();
    $this->resize = new Resize([], 'resize', []);

    $this->setSelection();
  }

  /**
   * This method will be removed.
   */
  public function test() {
    $this->assertEquals('test', 'test');
  }

  /**
   * {@inheritDoc}
   */
  public function getRequest() {
    $request = Request::create("/imce", 'POST', [
      'jsop' => 'resize',
      'token' => 'LLuA1R0aUOzoduSJkJxN5aoHVdJnQk8LbTBgdivOU4Y',
      'active_path' => '.',
      'selection' => [
        './ciandt.jpg',
      ],
      'width' => '315',
      'height' => '210',
      'copy' => '0',
    ]);
    $session = new Session();
    $session->set('imce_active_path', '.');
    $request->setSession($session);

    return $request;
  }

  /**
   * Set the ImceFM::selection[].
   */
  public function setSelection() {
    $this->imceFM->selection[] = $this->imceFM->createItem(
      'file', "ciandt.jpg", ['path' => '.']
    );
    // $this->imceFM->getConf()
    $this->imceFM->selection[0]->parent = new ImceFolder('.', $this->getConf());
    $this->imceFM->selection[0]->parent->setFm($this->imceFM);
    $this->imceFM->selection[0]->parent->setPath('.');
  }

  /**
   * Get permissions settings.
   *
   * @return array
   *   Return the array with permissions.
   */
  public function getConf() {
    return [
      'permissions' => ['all' => TRUE],
    ];
  }

  /**
   * Set the parameter copy.
   */
  public function setParameterCopy($copy) {
    $this->imceFM->request->request->add(['copy' => $copy]);
  }

  /**
   * Get plugins definations to new folder.
   */
  public function getPluginDefinations() {
    return [
      'weight' => 0,
      'operations' => [
        'resize' => 'opResize',
      ],
      'id' => 'resize',
      'label' => 'Resize',
      'class' => 'Drupal\imce\Plugin\ImcePlugin\Resize',
      'provider' => 'imce',
    ];
  }

  /**
   * Test Resize::permissionInfo()
   */
  public function testPermissiomInfo() {
    $permissionInfo = $this->resize->permissionInfo();
    $this->assertIsArray($permissionInfo);
    $this->assertTrue(in_array($this->t('Resize images'), $permissionInfo));
  }

  /**
   * Test resizing the image by making a image copy.
   */
  public function testResizeImageWithCopy() {
    $this->setParameterCopy(1);
    $this->resize->opResize($this->imceFM);
    list($width, $height) = getimagesize(PublicStream::basePath() . '/ciandt_0.jpg');
    $this->assertEquals($width, 315);
    $this->assertEquals($height, 210);
  }

  /**
   * Test image resizing without copy.
   */
  public function testResizeImageWithoutCopy() {
    $this->setParameterCopy(0);
    $this->resize->opResize($this->imceFM);
    list($width, $height) = getimagesize(PublicStream::basePath() . '/ciandt.jpg');
    $this->assertEquals($width, 315);
    $this->assertEquals($height, 210);
  }

  /**
   * Teste messages on context ImcePlugin\Resize.
   */
  public function testMessages() {
    $messages = $this->imceFM->getMessages();
    $this->assertIsArray($messages);
    $this->assertEquals([], $messages);
  }

  /**
   * Test Resize type.
   */
  public function testCore() {
    $this->assertInstanceOf(ImcePluginInterface::class, $this->resize);
  }

  /**
   * Test resize operation.
   */
  public function testOperation() {
    $this->assertEquals($this->imceFM->getOp(), 'resize');
  }

}
