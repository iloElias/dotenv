<?php

use Ilias\Dotenv\Environment;
use Ilias\Dotenv\Exceptions\EnvironmentNotFound;
use Ilias\Dotenv\Helper;
use PHPUnit\Framework\TestCase;

class EnvironmentTest extends TestCase
{
  private $envFile;

  protected function setUp(): void
  {
    Environment::$initialized = false;
    Environment::$vars = [];

    $this->envFile = __DIR__ . '/.env';
  }

  protected function tearDown(): void
  {
    if (file_exists($this->envFile)) {
      unlink($this->envFile);
    }
  }

  public function testSetupInitializesEnvironment()
  {
    $envContent = "APP_ENV=testing\nAPP_DEBUG=true";
    file_put_contents($this->envFile, $envContent);

    Environment::setup($this->envFile);

    $this->assertTrue(Environment::$initialized);
    $this->assertEquals('testing', getenv('APP_ENV'));
    $this->assertEquals('true', getenv('APP_DEBUG'));
  }

  public function testSetupThrowsExceptionWhenEnvFileNotFound()
  {
    $this->expectException(EnvironmentNotFound::class);

    Environment::setup('/invalid/path/.env');
  }

  public function testEnvHelperFunction()
  {
    $envContent = "APP_ENV=testing\nAPP_DEBUG=true";
    file_put_contents($this->envFile, $envContent);

    Environment::setup($this->envFile);

    $this->assertEquals('testing', Helper::env('APP_ENV'));
    $this->assertEquals('true', Helper::env('APP_DEBUG'));
  }

  public function testEnvHelperFunctionWithDefault()
  {
    file_put_contents($this->envFile, "APP_ENV=testing\nAPP_DEBUG=true");
    Environment::setup($this->envFile);

    $this->assertEquals('default_value', Helper::env('NON_EXISTENT_KEY', 'default_value'));
  }

  public function testNormalizeEnvValue()
  {
    $normalizeMethod = new \ReflectionMethod(Environment::class, 'normalizeEnvValue');
    $normalizeMethod->setAccessible(true);

    $this->assertEquals('true', $normalizeMethod->invoke(null, 'true'));
    $this->assertEquals('false', $normalizeMethod->invoke(null, 'false'));
    $this->assertEquals('', $normalizeMethod->invoke(null, 'empty'));
    $this->assertNull($normalizeMethod->invoke(null, 'null'));
    $this->assertEquals('some_value', $normalizeMethod->invoke(null, 'some_value'));
  }

  public function testParseEnvLine()
  {
    $parseMethod = new \ReflectionMethod(Environment::class, 'parseEnvLine');
    $parseMethod->setAccessible(true);

    $this->assertEquals(['APP_ENV', 'testing'], $parseMethod->invoke(null, 'APP_ENV=testing'));
    $this->assertEquals(['APP_DEBUG', 'true'], $parseMethod->invoke(null, 'APP_DEBUG=true'));
  }

  public function testIsValidEnvLine()
  {
    $isValidMethod = new \ReflectionMethod(Environment::class, 'isValidEnvLine');
    $isValidMethod->setAccessible(true);

    $this->assertTrue($isValidMethod->invoke(null, 'APP_ENV=testing'));
    $this->assertFalse($isValidMethod->invoke(null, '# This is a comment'));
    $this->assertFalse($isValidMethod->invoke(null, ''));
  }
}
