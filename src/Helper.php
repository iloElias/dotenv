<?php

namespace Ilias\Dotenv;

class Helper
{
  public static function env($key, $default = '')
  {
    Environment::setup();
    $value = getenv($key);
    return ($value !== false) ? $value : $default;
  }
}
