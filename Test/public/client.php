<?php

define("API_ENDPOINT", "http://rest/api/v1");

use Rest\Client\Client;
use Rest\Response;
use Rest\Client\Strategy\BasicAuthentication;
use Rest\Logging\SimpleLogger;

require_once(__DIR__ . "/../bootstrap.php");

$client = new Client(API_ENDPOINT, new BasicAuthentication("foo", "bar"));
$client
	->enableSSL(false)
	->setLogger(new SimpleLogger());
	

// GET example
$response = $client->get("/user");

// GET example with callback
$client->get("/user", function(Response $response){
});

// GET example with magic call
$response = $client->getUser();

// POST example
$response = $client->post("/user", ["email" => "foo@bar.com"]);

// POST example with callback
$client->post("/user", ["email" => "foo@bar.com"], function(Response $response){
});

// POST example with magic call
$response = $client->postUser(["email" => "foo@bar.com"]);

// or
$response = $client->user()->post(["email" => "foo@bar.com"]);

// PUT example
$response = $client->put("/user/1", ["email" => "foo@bar.com"]);

// PUT example with callback
$client->put("/user/1", ["email" => "foo@bar.com"], function(Response $response){
});

// PUT example with magic call
$response = $client->user(1)->put(["email" => "foo@bar.com"]);
	
//* PATCH example
$response = $client->patch("/user/1", ["email" => "foo@bar.com"]);
	
// PATCH example with callback
$client->patch("/user/1", ["email" => "foo@bar.com"], function(Response $response){
});

//* PATCH example with magic call
$response = $client->user(1)->patch(["email" => "foo@bar.com"]);
	
// DELETE example
$response = $client->delete("/user/1");
	
// DELETE example with callback
$client->delete("/user/1", function(Response $response){
});

// DELETE example with magic call
$response = $client->deleteUser(1);

// or
$response = $client->user(1)->delete();


/**
 * display logs
 */
foreach ($client->getLogger()->getLogs() as $request) {
	$response = $client->getLogger()->getLogs()[$request];
	
	echo "<h4>{$request->getMethod()} {$request->getURI()}</h4>";
	echo "<div>HTTP code: " . $response->getCode() . "</div>";
	echo "<div>Response headers:</div>";
	echo "<ul>";
	
	foreach ($response->getHeaders() as $name => $value) {
		echo "<li>{$name}: {$value}</li>";
	}
	
	echo "</ul>";
	
	if (trim($response->getContent())) {
		echo "<div>Content: " . $response . "</div>";
	}
	
	echo "<hr>";
}
