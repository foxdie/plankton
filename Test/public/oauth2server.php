<?php

require_once(__DIR__ . "/../bootstrap.php");

use Rest\Server\Server;
use OAuth2\Controller\AccessTokenController;
use OAuth2\Controller\RefreshTokenController;
use OAuth2\Grant\ClientCredentialsGrant;
use OAuth2\Middleware\ClientCredentialsMiddleware;
use OAuth2\Provider\MemoryProvider;
use Test\Controller\APIController;

// access token provider
$provider = new MemoryProvider();
$provider->addClient(1228, "56fdd11d6ca0c6960fbaa4d07acb65a881d5d145"); // @todo don't force numeric ids (could be login, email, ...)

$server = new Server();

$server
	->addMiddleware(new ClientCredentialsMiddleware($provider))	// handle grant and authorize requests
	->registerController(new AccessTokenController($provider))	// create access token
	->registerController(new RefreshTokenController())			// refresh access token
	->registerController(new APIController())
	->run();
