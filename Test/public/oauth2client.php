<?php

define("CLIENT_ID", 			1228);
define("CLIENT_SECRET", 		"56fdd11d6ca0c6960fbaa4d07acb65a881d5d145");
define("AUTHENTICATION_URL", 	"http://rest/api/v2/token");

use Rest\Client\Client;
use Rest\Response;
use Rest\Client\Auth\ClientCredentialsAuthentication;
use Rest\Logging\XMLLogger;

require_once(__DIR__ . "/../bootstrap.php");


$auth = new ClientCredentialsAuthentication(
	CLIENT_ID, 
	CLIENT_SECRET,
	AUTHENTICATION_URL
);

$client = new Client("http://rest/api/v2", $auth);

$client
	->enableSSL(false)
	->setLogger(new XMLLogger())
	->get("/user/1", function(Response $response) use ($client){
		header("Content-type: text/xml");
		echo $client->getLogger()->getLogs()->asXML();
	});
