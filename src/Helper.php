<?php

namespace Ilias\Choir\Bootstrap;

class Helper
{
  public static function env($key, $default = '')
  {
    return getenv($key) ?? $default;
  }
}
