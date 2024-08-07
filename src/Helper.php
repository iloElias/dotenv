<?php

namespace Ilias\Dotenv;

class Helper
{
  public static function env($key, $default = '')
  {
    Environment::setup();
    return getenv($key) ?? $default;
  }
}
