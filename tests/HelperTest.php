<?php

use Ilias\Dotenv\Helper;
use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
  public function testEnvReturnsValue()
  {
    putenv('TEST_KEY=TEST_VALUE');
    $this->assertEquals('TEST_VALUE', Helper::env('TEST_KEY'));
  }

  public function testEnvReturnsDefaultWhenKeyNotFound()
  {
    $this->assertEquals('default', Helper::env('NON_EXISTENT_KEY', 'default'));
  }
}
