<?php

namespace Drupal\Tests\imce\Kernel\Plugin\ImcePlugin;

use Drupal\imce\ImcePluginInterface;
use Drupal\imce\Plugin\ImcePlugin\Upload;
use Drupal\Tests\imce\Kernel\Plugin\KernelTestBasePlugin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Kernel tests for Imce plugins for Imce Plugin Core.
 *
 * @group imce
 */
class UploadTest extends KernelTestBasePlugin {

  /**
   * The Imce ckeditor plugin.
   *
   * @var \Drupal\imce\Plugin\ImcePlugin\Upload
   */
  public $upload;

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
    'system',
    'imce',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() : void {
    parent::setUp();
    $this->imceFM = $this->getImceFM();
    $this->upload = new Upload([], "upload", $this->getPluginDefinations());
  }

  /**
   * {@inheritDoc}
   */
  public function getRequest() {
    $request = Request::create("/imce", 'POST', [
      'jsop' => 'upload',
      'token' => 'LLuA1R0aUOzoduSJkJxN5aoHVdJnQk8LbTBgdivOU4Y',
      'active_path' => '.',
      'files[imce][]' => 'file.txt',
    ]);
    $session = new Session();
    $session->set('imce_active_path', '.');
    $request->setSession($session);

    return $request;
  }

  /**
   * This method will be removed.
   */
  public function test() {
    $this->assertEquals('test', 'test');
  }

  /**
   * Test Upload::permissionInfo()
   */
  public function testPermissionInfo() {
    $permissionInfo = $this->upload->permissionInfo();
    $this->assertIsArray($permissionInfo);
    $this->assertTrue(in_array('Upload files', $permissionInfo));
  }

  /**
   * Teste messages on context ImcePlugin\Upload.
   */
  public function testMessages() {
    $messages = $this->imceFM->getMessages();
    $this->assertIsArray($messages);
    $this->assertEquals([], $messages);
  }

  /**
   * Test Upload type.
   */
  public function testCore() {
    $this->assertInstanceOf(ImcePluginInterface::class, $this->upload);
  }

  /**
   * Test upload operation.
   */
  public function testOperation() {
    $this->assertEquals($this->imceFM->getOp(), 'upload');
  }

}
