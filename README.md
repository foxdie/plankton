

# RESTful API microframework

## Requirements

 - PHP 7.2
 - PHP cURL extension

## Installation

composer require foxdie/rest

## Client
### Create a client
	use Rest\Client\Client;
	use Rest\Client\Response;
	
	$client = new Client("http://rest/api/v1");
Full example here: https://github.com/foxdie/rest/blob/master/Test/public/client.php
### GET example
    $client->get("/user", function(Response $response){
		echo $response;
	});
### POST example
	$client->post("/user", ["email" => "foo@bar.com"], function(Response $response){
		echo $response->getLocation();
	});
### PUT example
	$client->put("/user/1", ["email" => "foo@bar.com"], function(Response $response){
		echo $response;
	});
### PATCH example
	$client->patch("/user/1", ["email" => "foo@bar.com"], function(Response $response){
		echo $response;
	});
### DELETE example
	$client->delete("/user/1", function(Response $response){
		echo $response;
	});
## Server
### Create a server
	use Rest\Server\Server;

	$server = new Server();
	$server->run();
### Create a controller to handle requests
	class APIController extends Controller{
		/**
		 * @Route(/user/{id})
		 * @Method(GET)
		 */
		public function getUser(int $id, Request $request): Response{
			// ...
		}
	}
Full example here : https://github.com/foxdie/rest/blob/master/Test/Controller/APIController.php
### Register the controller
	use Rest\Server\Server;

	$server = new Server();
	$server
		->registerController(new APIController());
		->run();
### Create a middleware
	use Rest\Server\Request;
	use Rest\Server\Response;
	use Rest\Server\Middleware;
	use Rest\Server\RequestDispatcher;
	
	class BasicAuthenticationMiddleware implements Middleware{
		public function process(Request $request, RequestDispatcher $dispatcher): Response{
			// ...
			return $dispatcher->process($request);
		}
	}
Full example here: https://github.com/foxdie/rest/blob/master/Test/Middleware/BasicAuthenticationMiddleware.php
### Register the middleware
	use Rest\Server\Server;

	$server = new Server();
	$server
		->addMiddleware(new BasicAuthenticationMiddleware())
		->registerController(new APIController())
		->run();
## OAuth2
### Client Credentials Grant
#### Client
	use Rest\Client\Client;
	use Rest\Client\Strategy\ClientCredentialsAuthentication;
	use Rest\Response;
	
	$auth = new ClientCredentialsAuthentication(
		CLIENT_ID, 
		CLIENT_SECRET,
		AUTHENTICATION_URL
	);

	$client = new Client("http://rest/api/v2", $auth);
	
	$client->get("/user/1", function(Response $response){
		// ...
	});
Full example here: 	
https://github.com/foxdie/rest/blob/master/Test/public/oauth2client.php
#### Server
	use Rest\Server\Server;
	use OAuth2\Middleware\ClientCredentialsMiddleware;
	use OAuth2\Provider\MemoryProvider;
	use Test\Controller\APIController;
	
	// access token provider
	$provider = new MemoryProvider();
	$provider->addClient(CLIENT_ID, CLIENT_SECRET);
	
	$server = new Server();
	
	$server
		->addMiddleware(new ClientCredentialsMiddleware($provider))
		->registerController(new APIController())
		->run();
Full example here:
https://github.com/foxdie/rest/blob/master/Test/public/oauth2server.php
##### Create your own Access Token Provider
All you have to do is to implement the AccessTokenProvider interface :
	use Rest\OAuth2\Provider\AccessTokenProvider;
	use Rest\OAuth2\Token\AccessToken;
	use Rest\OAuth2\Token\BearerToken;


	class PDOProvider implements AccessTokenProvider{
		public function getAccessToken(string $client_id, string $client_secret): ?AccessToken{
			// return a new/issued Access Token if you find a client matching the authentication parameters (id + secret)
		}

		public function refreshToken(string $refreshToken): ?AccessToken{
			// return a new Access Token if the Refresh Token is valid
		}

		public function isValidAccessToken(string $token): bool{
			// authorize or not the given Access Token
		}
	}