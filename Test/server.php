<?php

require_once(__DIR__ . "/bootstrap.php");


use Rest\Server\Server;
use Test\Controller\APIController;

$server = new Server();
$server->registerController(new APIController())->run();
