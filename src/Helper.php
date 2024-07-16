<?php

namespace Ilias\Dotenv;

class Helper
{
  public static function env($key, $default = '')
  {
    return getenv($key) ?? $default;
  }
}
