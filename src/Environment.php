<?php

namespace Ilias\Dotenv;

use Ilias\Dotenv\Exceptions\EnvironmentNotFound;

class Environment
{
  private static string $envPathFile = '.env';
  public static bool $initialized = false;
  public static array $vars = [];

  public static function setup(string $customEnvPathFile = null): void
  {
    if (self::$initialized === false) {
      $envFile = $customEnvPathFile ?? self::$envPathFile;

      try {
        $envContent = self::loadEnvFile($envFile);
        if ($envContent === null) {
          throw new EnvironmentNotFound();
        }
      } catch (\Throwable $th) {
        throw new EnvironmentNotFound();
      }

      $envLines = explode("\n", $envContent);
      self::processEnvLines($envLines);
      self::$initialized = true;
    }
  }

  private static function loadEnvFile(string $envFile)
  {
    if (file_exists($envFile)) {
      return file_get_contents($envFile) ?: null;
    }
    return null;
  }

  private static function processEnvLines(array $envLines): void
  {
    foreach ($envLines as $envLine) {
      if (self::isValidEnvLine($envLine)) {
        [$name, $value] = self::parseEnvLine($envLine);
        $value = self::normalizeEnvValue($value);

        self::setEnvironmentVariable($name, $value);
      }
    }
  }

  private static function isValidEnvLine(string $envLine): bool
  {
    return $envLine !== '' && $envLine !== '0' && strpos($envLine, '#') !== 0;
  }

  private static function parseEnvLine(string $envLine): array
  {
    [$name, $value] = explode('=', $envLine, 2);
    return [trim($name), trim(str_replace('"', '', $value))];
  }

  private static function normalizeEnvValue(string $value)
  {
    switch (strtolower($value)) {
      case 'true':
      case '(true)':
        return 'true';
      case 'false':
      case '(false)':
        return 'false';
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

  private static function setEnvironmentVariable(string $name, $value): void
  {
    putenv(sprintf('%s=%s', $name, $value));
    self::$vars[$name] = $value;
  }
}
