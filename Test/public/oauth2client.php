<?php

use Rest\Client\Client;
use Rest\Response;

require_once(__DIR__ . "/../bootstrap.php");

$client = new Client("http://rest/api/v2");
$client->enableSSL(false);

/**
 request token
    grant_type=client_credentials
    client_id=CLIENT_ID
    client_secret=CLIENT_SECRET
request API
    header: Authorization: Bearer ACCESS_TOKEN
refresh token
    grant_type=refresh_token
    client_id=CLIENT_ID
    client_secret=CLIENT_SECRET
    refresh_token=REFRESH_TOKEN
 */
$client->get("/token", function(Response $response){
	echo "<h4>GET /api/v2/token</h4>";
	debug($response);
});

function debug(Response $response){
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
