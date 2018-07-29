<?php

require_once(__DIR__ . "/../../vendor/autoload.php");

use Plankton\Server\Server;
use Plankton\OAuth2\Middleware\ClientCredentialsMiddleware;
use Plankton\OAuth2\Provider\MemoryProvider;
use Test\Controller\APIController;


// Access Token provider
$provider = new MemoryProvider();
$provider->addClient("foo@bar.com", "56fdd11d6ca0c6960fbaa4d07acb65a881d5d145");

$server = new Server();
$server
	->addMiddleware(new ClientCredentialsMiddleware($provider))
	->registerController(new APIController())
	->run();
