<?php

define("API_ENDPOINT", 			$_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . "/api/v2");
define("ACCESS_TOKEN_ENDPOINT", "http://plankton/api/v2/token");
define("CLIENT_ID", 			"foo@bar.com");
define("CLIENT_SECRET", 		"56fdd11d6ca0c6960fbaa4d07acb65a881d5d145");

require_once(__DIR__ . "/../../vendor/autoload.php");

use Plankton\Client\Client;
use Plankton\Response;
use Plankton\Client\Strategy\ClientCredentialsAuthentication;
use Plankton\Logging\XMLLogger;


// authentication, access/refresh token requests will be performed automatically by the client
$auth = new ClientCredentialsAuthentication(
	CLIENT_ID, 
	CLIENT_SECRET,
	ACCESS_TOKEN_ENDPOINT
);

// reuse an issued token
// $auth->setAccessToken(new Plankton\OAuth2\Token\BearerToken(...));

$client = new Client(API_ENDPOINT, $auth);

$client
	->enableSSL(false)
	->setLogger(new XMLLogger())
	->get("/users/1", function(Response $response) use ($client, $auth){
		header("Content-type: text/xml");
		echo $client->getLogger()->getLogs()->asXML();
		
		// if you want to store the issued token and reuse it later
		$token = $auth->getAccessToken();
	});

