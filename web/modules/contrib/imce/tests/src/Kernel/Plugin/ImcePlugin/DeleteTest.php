<?php

namespace Drupal\Tests\imce\Kernel\Plugin\ImcePlugin;

use Drupal\Core\StreamWrapper\PublicStream;
use Drupal\imce\ImceFile;
use Drupal\imce\ImceFolder;
use Drupal\imce\ImcePluginInterface;
use Drupal\imce\Plugin\ImcePlugin\Delete;
use Drupal\Tests\imce\Kernel\Plugin\KernelTestBasePlugin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Kernel tests for Imce plugins for Imce Plugin Core.
 *
 * @group imce
 */
class DeleteTest extends KernelTestBasePlugin {

  /**
   * The Imce ckeditor plugin.
   *
   * @var \Drupal\imce\Plugin\ImcePlugin\Delete
   */
  public $delete;

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
    $this->delete = new Delete([], "delete", []);

    $this->getTestFileUri();
    $this->setSelectionFile();
  }

  /**
   * {@inheritDoc}
   */
  public function getRequest() {
    $request = Request::create("/imce", 'POST', [
      'jsop' => 'delete',
      'token' => 'LLuA1R0aUOzoduSJkJxN5aoHVdJnQk8LbTBgdivOU4Y',
      'active_path' => '.',
      'selection' => ['ciandt.jpg'],
    ]);
    $session = new Session();
    $session->set('imce_active_path', '.');
    $request->setSession($session);

    return $request;
  }

  /**
   * Set the ImceFM::selection[].
   */
  public function setSelectionFile() {
    $this->imceFM->selection[] = $this->imceFM->createItem(
      'file', "ciandt.jpg", ['path' => '.']
    );
    // $this->imceFM->getConf()
    $this->imceFM->selection[0] = new ImceFile('ciandt.jpg');
    $this->imceFM->selection[0]->setFm($this->imceFM);
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
   * Test file delete.
   */
  public function testFileDelete() {
    $this->assertTrue(file_exists(PublicStream::basePath() . '/ciandt.jpg'));
    $this->delete->opDelete($this->imceFM);
    $this->assertTrue(!file_exists(PublicStream::basePath() . '/ciandt.jpg'));
  }

  /**
   * Test Delete::permissionInfo()
   */
  public function testPermissiomInfo() {
    $permissionInfo = $this->delete->permissionInfo();
    $this->assertIsArray($permissionInfo);
    $this->assertTrue(in_array('Delete files', $permissionInfo));
    $this->assertTrue(in_array('Delete subfolders', $permissionInfo));
  }

  /**
   * Teste messages on context ImcePlugin\Delete.
   */
  public function testMessages() {
    $messages = $this->imceFM->getMessages();
    $this->assertIsArray($messages);
    $this->assertEquals([], $messages);
  }

  /**
   * Test Delete type.
   */
  public function testCore() {
    $this->assertInstanceOf(ImcePluginInterface::class, $this->delete);
  }

  /**
   * Test operation of delete.
   */
  public function testOperation() {
    $this->assertEquals($this->imceFM->getOp(), 'delete');
  }

}
