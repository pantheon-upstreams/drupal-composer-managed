<?php

namespace Drupal\Tests\imce\Unit;

use Drupal\imce\ImceFile;
use Drupal\Tests\UnitTestCase;

/**
 * Test ImceFile.
 *
 * @group imce
 */
class ImceFileTest extends UnitTestCase {

  /**
   * Imce File.
   *
   * @var \Drupal\imce\ImceFile
   */
  protected $imceFile;

  /**
   * {@inheritdoc}
   */
  public function setUp() : void {
    parent::setUp();
    $this->imceFile = $this->createMock(ImceFile::class);
  }

  /**
   * Test type.
   */
  public function testType() {
    $this->assertNotEmpty($this->imceFile->type);
    $this->assertIsString($this->imceFile->type);
    $this->assertEquals('file', $this->imceFile->type);
  }

}
