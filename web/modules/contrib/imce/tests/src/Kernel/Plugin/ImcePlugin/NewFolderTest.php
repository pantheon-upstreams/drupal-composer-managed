<?php

namespace Drupal\Tests\imce\Kernel\Plugin\ImcePlugin;

use Drupal\imce\Imce;
use Drupal\imce\ImcePluginInterface;
use Drupal\imce\Plugin\ImcePlugin\Newfolder;
use Drupal\Tests\imce\Kernel\Plugin\KernelTestBasePlugin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Kernel tests for Imce plugins for Imce Plugin NewFolder.
 *
 * @group imce
 */
class NewFolderTest extends KernelTestBasePlugin {

  /**
   * The Imce ckeditor plugin.
   *
   * @var \Drupal\imce\Plugin\ImcePlugin\Newfolder
   */
  public $newFolder;

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
    $this->newFolder = new Newfolder([], 'newfolder', []);

    $this->newFolder->opNewfolder($this->imceFM);
  }

  /**
   * {@inheritDoc}
   */
  public function getRequest() {
    $request = Request::create("/imce", 'POST', [
      'jsop' => 'newfolder',
      'token' => 'LLuA1R0aUOzoduSJkJxN5aoHVdJnQk8LbTBgdivOU4Y',
      'active_path' => '.',
      'newfolder' => 'folder-test',
    ]);
    $session = new Session();
    $session->set('imce_active_path', '.');
    $request->setSession($session);

    return $request;
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
   * Get plugins definations to new folder.
   */
  public function getPluginDefinations() {
    return [
      'weight' => '-15',
      'operations' => [
        'newfolder' => "opNewfolder",
      ],
      'id' => "newfolder",
      'label' => "New Folder",
      'class' => "Drupal\imce\Plugin\ImcePlugin\Newfolder",
      'provider' => "imce",
    ];
  }

  /**
   * Test to NewFolder::permissionInfo().
   */
  public function testPermissiomInfo() {
    $permissionInfo = $this->newFolder->permissionInfo();
    $this->assertIsArray($permissionInfo);
    $this->assertTrue(in_array('Create subfolders', $permissionInfo));
  }

  /**
   * Test if folder was created.
   */
  public function testFolderCreate() {
    $uriFolder = Imce::joinPaths(
      $this->imceFM->activeFolder->getUri(), $this->imceFM->getPost('newfolder')
    );

    $this->assertIsString($uriFolder);
    $this->assertTrue(file_exists($uriFolder));
  }

  /**
   * Teste messages on context ImcePlugin\NewFolder.
   */
  public function testMessages() {
    $messages = $this->imceFM->getMessages();
    $this->assertIsArray($messages);
    $this->assertEquals([], $messages);
  }

  /**
   * Test NewFolder type.
   */
  public function testCore() {
    $this->assertInstanceOf(ImcePluginInterface::class, $this->newFolder);
  }

  /**
   * Test operation of newFolder.
   */
  public function testOperation() {
    $this->assertEquals($this->imceFM->getOp(), 'newfolder');
  }

}
