<?php

use Rest\Client\Client;
use Rest\Client\Response;


require_once(__DIR__ . "/bootstrap.php");

$client = new Client("http://rest/api/v1");

$client->enableSSL(false);

/**
 * GET example
 */
$client->get("/user/1", function(Response $response){
	echo "<h4>GET /user/1</h4>";
	echo "<div>code: " . $response->getCode() . "</div>";
	foreach ($response->getHeaders() as $name => $value) {
		echo "<div>{$name}: {$value}</div>";
	}
	echo "<div>Content: " . $response . "</div>";
});

/**
 * POST example
 */
$postData = ["email" => "dummy@localhost"];

$client->post("/user", $postData, function(Response $response){
	echo "<h4>POST /user</h4>";
	echo "<div>code: " . $response->getCode() . "</div>";
	foreach ($response->getHeaders() as $name => $value) {
		echo "<div>{$name}: {$value}</div>";
	}
	echo "<div>Content: " . $response . "</div>";
});
