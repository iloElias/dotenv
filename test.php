<?php

use Ilias\Dotenv\Environment;

require_once __DIR__ . '/vendor/autoload.php';

Environment::setup();

var_dump(Environment::$vars);

var_dump(getenv('APP_DEBUG'));
