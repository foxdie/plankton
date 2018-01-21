<?php

require_once(__DIR__ . "/../bootstrap.php");


use Rest\Server\Server;
use Test\Controller\APIController;
use Test\Middleware\BasicAuthenticationMiddleware;
use Test\Middleware\LogMiddleware;

$server = new Server();

$server
	->addMiddleware(new BasicAuthenticationMiddleware())
	->addMiddleware(new LogMiddleware())
	->registerController(new APIController())
	->run();
