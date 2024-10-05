<?php

namespace Ilias\Dotenv;

use Ilias\Dotenv\Exceptions\EnvironmentNotFound;

class Environment
{
  public const THROW_EXCEPTION = 1;
  public const SUPPRESS_EXCEPTION = 2;

  private static string $envPathFile = '.env';
  public static bool $initialized = false;
  public static array $vars = [];

  public static function setup(string $customEnvPathFile = null, int $exceptionHandle = Environment::THROW_EXCEPTION): void
  {
    if (self::$initialized === false) {
      $envFile = $customEnvPathFile ?? self::$envPathFile;

      $envContent = self::loadEnvFile($envFile);
      if ($envContent === null && $exceptionHandle === Environment::THROW_EXCEPTION) {
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
    return $envLine !== '' && $envLine !== '0' && !empty(trim(explode('#', $envLine)[0])) && strpos($envLine, '#') !== 0;
  }

  private static function parseEnvLine(string $envLine): array
  {
    [$name, $value] = explode('=', explode('#', $envLine)[0], 2);
    return [trim($name), trim(str_replace('"', '', $value))];
  }

  private static function normalizeEnvValue(string $value)
  {
    $lowerValue = strtolower($value);
    if (strpos($value, '#') !== false) {
      $value = substr($value, 0, strpos($value, '#'));
    }

    if (in_array($lowerValue, ['true', '(true)'], true)) {
      return 'true';
    }

    if (in_array($lowerValue, ['false', '(false)'], true)) {
      return 'false';
    }

    if (in_array($lowerValue, ['empty', '(empty)'], true)) {
      return '';
    }

    if (in_array($lowerValue, ['null', '(null)'], true)) {
      return null;
    }

    return trim($value);
  }

  private static function setEnvironmentVariable(string $name, $value): void
  {
    putenv(sprintf('%s=%s', $name, $value));
    self::$vars[$name] = $value;
  }
}
