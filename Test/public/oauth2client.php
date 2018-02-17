<?php

use Rest\Client\Client;
use Rest\Response;
use Rest\Client\Auth\ClientCredentialsAuthentication;
use Rest\Logging\XMLLogger;

require_once(__DIR__ . "/../bootstrap.php");


$auth = new ClientCredentialsAuthentication(
	"1228", 
	sha1(uniqid(mt_rand(), true)), 
	"http://rest/api/v2/token"
);

$client = new Client("http://rest/api/v2", $auth);

$client
	->enableSSL(false)
	->setLogger(new XMLLogger())
	->get("/user/1", function(Response $response) use ($client){
		header("Content-type: text/xml");
		echo $client->getLogger()->getLogs()->asXML();
	});
