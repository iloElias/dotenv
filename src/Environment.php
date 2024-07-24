<?php

namespace Ilias\Dotenv;

use Ilias\Dotenv\Exceptions\EnvironmentNotFound;

class Environment
{
  private const ENV_FILE_PATH = '.env';
  public static array $vars = [];

  public static function setup(string $customPath = null): void
  {
    $envFile = $customPath ?? ((!empty($_SERVER["DOCUMENT_ROOT"]) ? $_SERVER["DOCUMENT_ROOT"] : __DIR__) . DIRECTORY_SEPARATOR . self::ENV_FILE_PATH);

    try {
      $envContent = self::loadEnvFile($envFile);
      if ($envContent === null) {
        throw new \Exception("Environment file not found");
      }
    } catch (\Throwable $th) {
      throw new EnvironmentNotFound();
    }

    $envLines = explode("\n", $envContent);
    self::processEnvLines($envLines);
  }

  public static function loadEnvFile(string $envFile)
  {
    if (file_exists($envFile)) {
      return file_get_contents($envFile) ?? null;
    }
  }

  public static function processEnvLines(array $envLines): void
  {
    foreach ($envLines as $envLine) {
      if (self::isValidEnvLine($envLine)) {
        [$name, $value] = self::parseEnvLine($envLine);
        $value = self::normalizeEnvValue($value);

        self::setEnvironmentVariable($name, $value);
      }
    }
  }

  public static function isValidEnvLine(string $envLine): bool
  {
    return $envLine !== '' && $envLine !== '0' && strpos($envLine, '#') !== 0;
  }

  public static function parseEnvLine(string $envLine): array
  {
    [$name, $value] = explode('=', $envLine, 2);
    return [trim($name), trim(str_replace('"', '', $value))];
  }

  public static function normalizeEnvValue(string $value)
  {
    switch (strtolower($value)) {
      case 'true':
      case '(true)':
        return true;
      case 'false':
      case '(false)':
        return false;
      case 'empty':
      case '(empty)':
        return '';
      case 'null':
      case '(null)':
        return null;
      default:
        return $value;
    }
  }

  public static function setEnvironmentVariable(string $name, $value): void
  {
    putenv(sprintf('%s=%s', $name, $value));
    self::$vars[$name] = $value;
  }
}
