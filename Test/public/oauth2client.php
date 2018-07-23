<?php

use Rest\Client\Client;
use Rest\Response;
use Rest\Client\Auth\ClientCredentialsAuthentication;
use Rest\Logging\XMLLogger;

require_once(__DIR__ . "/../bootstrap.php");


$auth = new ClientCredentialsAuthentication(
	"1228", 
	"56fdd11d6ca0c6960fbaa4d07acb65a881d5d145", //sha1(uniqid(mt_rand(), true)), 
	"http://rest/api/v2/token" //@todo cannot be customized (@see AccessTokenController)
);

$client = new Client("http://rest/api/v2", $auth);

$client
	->enableSSL(false)
	->setLogger(new XMLLogger())
	->get("/user/1", function(Response $response) use ($client){
		header("Content-type: text/xml");
		echo $client->getLogger()->getLogs()->asXML();
	});
