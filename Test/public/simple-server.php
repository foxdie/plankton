<?php

require_once(__DIR__ . "/../../vendor/autoload.php");

use Plankton\Server\Server;
use Test\Controller\APIController;
use Test\Middleware\BasicAuthenticationMiddleware;
use Test\Middleware\LogMiddleware;


$server = new Server();

$server
	->addMiddleware(new BasicAuthenticationMiddleware())
	->addMiddleware(new LogMiddleware())
	->registerController(new APIController())
	->run();
