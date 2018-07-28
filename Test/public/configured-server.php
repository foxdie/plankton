<?php

define("CONFIG_PATH", realpath(__DIR__ . "/../config/server.yml"));

require_once(__DIR__ . "/../bootstrap.php");
require_once(__DIR__ . "/../../vendor/autoload.php");

use Plankton\Server\Server;
use Plankton\Server\Config;


$server = new Server(new Config(CONFIG_PATH));
$server->run();
