<?php

require_once(__DIR__ . "/bootstrap.php");
require_once(__DIR__ . "/Controller/APIController.php");

use Rest\Server\Controller;
use Rest\Server\Server;

$server = new Server();
$server->registerController(new APIController())->run();
