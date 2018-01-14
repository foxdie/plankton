<?php

use Rest\Client\Client;
use Rest\Client\Response;

require_once(__DIR__ . "/../bootstrap.php");

$client = new Client("http://rest/api/v1");

$client->enableSSL(false);

/**
 * GET example
 */
$client->get("/user", function(Response $response){
	echo "<h4>GET /api/v1/user/1</h4>";
	debug($response);
});

/**
 * POST example
 */
$data = ["email" => "postme@localhost"];

$client->post("/user", $data, function(Response $response){
	echo "<h4>POST /api/v1/user</h4>";
	debug($response);
});

/**
 * PUT example
 */
$data = ["email" => "putme@localhost"];

$client->put("/user/1", $data, function(Response $response){
	echo "<h4>PUT /api/v1/user/1</h4>";
	debug($response);
});
	
/**
 * PATCH example
 */
$data = ["email" => "patchme@localhost"];

$client->patch("/user/1", $data, function(Response $response){
	echo "<h4>PATCH /api/v1/user/1</h4>";
	debug($response);
});

/**
 * DELETE example
 */

$client->delete("/user/1", function(Response $response){
	echo "<h4>DELETE /api/v1/user</h4>";
	debug($response);
});

function debug(Response $response){
	echo "<div>code: " . $response->getCode() . "</div>";
	debug($response);
}
