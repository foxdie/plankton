<?php

require_once(__DIR__ . "/../bootstrap.php");

use Rest\Server\Server;
use OAuth2\Controller\AccessTokenController;
use OAuth2\Grant\ClientCredentialsGrant;
use OAuth2\Middleware\ClientCredentialsMiddleware;


$grant = new ClientCredentialsGrant;

$server = new Server();

$server
	->addMiddleware(new ClientCredentialsMiddleware())
	->registerController(new AccessTokenController())
	->run();
