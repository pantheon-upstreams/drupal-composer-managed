<?php

namespace Drupal\Tests\imce\Kernel\Plugin\ImcePlugin;

use Drupal\imce\ImcePluginInterface;
use Drupal\imce\Plugin\ImcePlugin\Core;
use Drupal\Tests\imce\Kernel\Plugin\KernelTestBasePlugin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Kernel tests for Imce plugins for Imce Plugin Core.
 *
 * @group imce
 */
class CoreTest extends KernelTestBasePlugin {

  /**
   * The Imce ckeditor plugin.
   *
   * @var \Drupal\imce\Plugin\ImcePlugin\Core
   */
  public $core;

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
    $this->core = new Core([], 'core', []);

    $this->core->opBrowse($this->imceFM);
  }

  /**
   * {@inheritDoc}
   */
  public function getRequest() {
    $request = Request::create("/imce", 'POST', [
      'jsop' => 'browse',
      'token' => 'LLuA1R0aUOzoduSJkJxN5aoHVdJnQk8LbTBgdivOU4Y',
      'active_path' => '.',
    ]);
    $session = new Session();
    $session->set('imce_active_path', '.');
    $request->setSession($session);

    return $request;
  }

  /**
   * The get settings.
   *
   * @return array
   *   Return settings array.
   */
  public function getConf() {
    return [
      'permissions' => ['all' => TRUE],
    ];
  }

  /**
   * Test core type.
   */
  public function testCore() {
    $this->assertInstanceOf(ImcePluginInterface::class, $this->core);
  }

  /**
   * Test ImceFM::tree.
   */
  public function testTree() {
    $this->assertIsArray($this->imceFM->tree);
    $this->assertTrue((count($this->imceFM->tree) > 0));
  }

  /**
   * Test subFolders.
   */
  public function testSubfolders() {
    $subFolders = $this->imceFM->activeFolder->subfolders;
    $this->assertIsArray($subFolders);
    $this->assertTrue((count($subFolders) > 0));
  }

  /**
   * Test Core::permissionInfo().
   */
  public function testPermissionInfo() {
    $permissionInfo = $this->core->permissionInfo();
    $this->assertIsArray($permissionInfo);
    $this->assertTrue(in_array('Browse files', $permissionInfo));
    $this->assertTrue(in_array('Browse subfolders', $permissionInfo));
  }

  /**
   * Test scan().
   */
  public function testScan() {
    $this->assertTrue(is_bool($this->imceFM->activeFolder->scanned));
    $this->assertTrue($this->imceFM->activeFolder->scanned);
  }

  /**
   * Teste messages on context ImcePlugin\Core.
   */
  public function testMessages() {
    $messages = $this->imceFM->getMessages();
    $this->assertIsArray($messages);
    $this->assertEquals([], $messages);
  }

  /**
   * Test operation of delete.
   */
  public function testOperation() {
    $this->assertEquals($this->imceFM->getOp(), 'browse');
  }

}
