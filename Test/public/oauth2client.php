<?php

define("API_ENDPOINT", 			"http://rest/api/v2");
define("ACCESS_TOKEN_ENDPOINT", "http://rest/api/v2/token");

define("CLIENT_ID", 			"foo@bar.com");
define("CLIENT_SECRET", 		"56fdd11d6ca0c6960fbaa4d07acb65a881d5d145");

use Rest\Client\Client;
use Rest\Response;
use Rest\Client\Strategy\ClientCredentialsAuthentication;
use Rest\Logging\XMLLogger;

require_once(__DIR__ . "/../bootstrap.php");

// authentication, access/refresh token requests will be performed automatically by the client
$auth = new ClientCredentialsAuthentication(
	CLIENT_ID, 
	CLIENT_SECRET,
	ACCESS_TOKEN_ENDPOINT
);

// reuse an issued token
// $auth->setAccessToken(new Rest\OAuth2\Token\BearerToken(...));

$client = new Client(API_ENDPOINT, $auth);

$client
	->enableSSL(false)
	->setLogger(new XMLLogger())
	->get("/user/1", function(Response $response) use ($client, $auth){
		header("Content-type: text/xml");
		echo $client->getLogger()->getLogs()->asXML();
		
		// if you want to store the issued token and reuse it later
		$token = $auth->getAccessToken();
	});

