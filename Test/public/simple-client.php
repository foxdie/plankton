<?php

define("API_ENDPOINT", $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . "/api/v1");

require_once(__DIR__ . "/../../vendor/autoload.php");

use Plankton\Client\Client;
use Plankton\Response;
use Plankton\Client\Strategy\BasicAuthentication;
use Plankton\Logging\SimpleLogger;


$client = new Client(API_ENDPOINT, new BasicAuthentication("foo", "bar"));
$client
	->enableSSL(false)
	->setLogger(new SimpleLogger());

// GET example
$response = $client->get("/users");

// GET example with callback
$client->get("/users", function(Response $response){
});

// GET example with magic call
$response = $client->getUser();

// POST example
$response = $client->post("/users", ["email" => "foo@bar.com"]);

// POST example with callback
$client->post("/users", ["email" => "foo@bar.com"], function(Response $response){
});

// POST example with magic call
$response = $client->postUser(["email" => "foo@bar.com"]);

// or
$response = $client->user()->post(["email" => "foo@bar.com"]);

// PUT example
$response = $client->put("/users/1", ["email" => "foo@bar.com"]);

// PUT example with callback
$client->put("/users/1", ["email" => "foo@bar.com"], function(Response $response){
});

// PUT example with magic call
$response = $client->user(1)->put(["email" => "foo@bar.com"]);
	
//* PATCH example
$response = $client->patch("/users/1", ["email" => "foo@bar.com"]);
	
// PATCH example with callback
$client->patch("/users/1", ["email" => "foo@bar.com"], function(Response $response){
});

//* PATCH example with magic call
$response = $client->user(1)->patch(["email" => "foo@bar.com"]);
	
// DELETE example
$response = $client->delete("/users/1");
	
// DELETE example with callback
$client->delete("/users/1", function(Response $response){
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
