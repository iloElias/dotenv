<?php

namespace Ilias\Dotenv;

class Helper
{
  public static function env($key, $default = '')
  {
    $value = getenv($key);
    return $value === false ? $default : $value;
  }
}
