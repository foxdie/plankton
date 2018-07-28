<?php

define("API_ENDPOINT", $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . "/api/v3");

require_once(__DIR__ . "/../../vendor/autoload.php");

use Plankton\Client\Client;
use Plankton\Response;
use Plankton\Logging\SimpleLogger;


$client = new Client(API_ENDPOINT);
$client
	->enableSSL(false)
	->setLogger(new SimpleLogger());
	
// GET example
$response = $client->get("/user");

// POST example
$response = $client->post("/user", ["email" => "foo@bar.com"]);

// PUT example
$response = $client->put("/user/1", ["email" => "foo@bar.com"]);

// PATCH example
$response = $client->patch("/user/1", ["email" => "foo@bar.com"]);

// DELETE example
$response = $client->delete("/user/1");

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
