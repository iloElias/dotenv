<?php

use Ilias\Dotenv\Environment;
use Ilias\Dotenv\Exceptions\EnvironmentNotFound;
use PHPUnit\Framework\TestCase;

class EnvironmentTest extends TestCase
{
  private $envFilePath;

  protected function setUp(): void
  {
    $this->envFilePath = __DIR__ . '/.env';

    if (file_exists($this->envFilePath)) {
      unlink($this->envFilePath);
    }

    file_put_contents($this->envFilePath, "TEST_KEY=TEST_VALUE\nBOOLEAN_TRUE=true\nBOOLEAN_FALSE=false\nEMPTY_VALUE=empty\nNULL_VALUE=null\n");
  }

  protected function tearDown(): void
  {
    if (file_exists($this->envFilePath)) {
      unlink($this->envFilePath);
    }
  }

  public function testSetupLoadsEnvironmentFile()
  {
    Environment::setup($this->envFilePath);

    $this->assertEquals('TEST_VALUE', getenv('TEST_KEY'));
    $this->assertEquals('TEST_VALUE', Environment::$vars['TEST_KEY']);
  }

  public function testSetupThrowsExceptionWhenEnvFileNotFound()
  {
    unlink($this->envFilePath);

    $this->expectException(EnvironmentNotFound::class);

    Environment::setup($this->envFilePath);
  }

  public function testValidEnvLine()
  {
    $this->assertTrue(Environment::isValidEnvLine('KEY=VALUE'));
    $this->assertFalse(Environment::isValidEnvLine('#COMMENT'));
    $this->assertFalse(Environment::isValidEnvLine(''));
    $this->assertFalse(Environment::isValidEnvLine('0'));
  }

  public function testParseEnvLine()
  {
    $parsedLine = Environment::parseEnvLine('KEY=VALUE');
    $this->assertEquals(['KEY', 'VALUE'], $parsedLine);
  }

  public function testNormalizeEnvValue()
  {
    $this->assertTrue(Environment::normalizeEnvValue('true'));
    $this->assertFalse(Environment::normalizeEnvValue('false'));
    $this->assertEquals('', Environment::normalizeEnvValue('empty'));
    $this->assertNull(Environment::normalizeEnvValue('null'));
    $this->assertEquals('value', Environment::normalizeEnvValue('value'));
  }

  public function testSetEnvironmentVariable()
  {
    Environment::setEnvironmentVariable('KEY', 'VALUE');
    $this->assertEquals('VALUE', getenv('KEY'));
    $this->assertEquals('VALUE', Environment::$vars['KEY']);
  }
}
