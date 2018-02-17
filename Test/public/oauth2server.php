<?php

require_once(__DIR__ . "/../bootstrap.php");

use Rest\Server\Server;
use OAuth2\Controller\AccessTokenController;
use OAuth2\Controller\RefreshTokenController;
use OAuth2\Grant\ClientCredentialsGrant;
use OAuth2\Middleware\ClientCredentialsMiddleware;
use OAuth2\Provider\MemoryProvider;


$grant = new ClientCredentialsGrant();

$provider = new MemoryProvider();
$provider->addClient(1027, "");

$server = new Server();

$server
	->addMiddleware(new ClientCredentialsMiddleware())
	->registerController(new AccessTokenController())
	->registerController(new RefreshTokenController())
	->run();
