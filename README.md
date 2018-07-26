

# RESTful API microframework

## Requirements

 - PHP 7.2
 - PHP cURL extension

## Installation

composer require foxdie/rest

## Client
### Create a client
	define("API_ENDPOINT", ""http://rest/api/v1");
	
	use Rest\Client\Client;
		
	$client = new Client(API_ENDPOINT);
Full example here: https://github.com/foxdie/rest/blob/master/Test/public/client.php
### GET example
	$response = $client->get("/user");
#### using callback
    $client->get("/user", function(Response $response){
		echo $response;
	});
####using magic
	$response = $client->getUser();
### POST example
	$response = $client->post("/user", ["email" => "foo@bar.com"]);
#### using callback
	$client->post("/user", ["email" => "foo@bar.com"], function(Response $response){
		echo $response->getLocation();
	});
####using magic
	$response = $client->postUser(["email" => "foo@bar.com"]);
	// or
	$response = $client->user()->post(["email" => "foo@bar.com"]);
### PUT example
	$response = $client->put("/user/1", ["email" => "foo@bar.com"]);
#### using callback
	$client->put("/user/1", ["email" => "foo@bar.com"], function(Response $response){
		echo $response;
	});
####using magic
	$response = $client->user(1)->put(["email" => "foo@bar.com"]);
### PATCH example
	$response = $client->patch("/user/1", ["email" => "foo@bar.com"]);
#### using callback	
	$client->patch("/user/1", ["email" => "foo@bar.com"], function(Response $response){
		echo $response;
	});
####using magic
	$response = $client->user(1)->patch(["email" => "foo@bar.com"]);
### DELETE example
	$response = $client->delete("/user/1");
#### using callback
	$client->delete("/user/1", function(Response $response){
		echo $response;
	});
####using magic
	$response = $client->deleteUser(1);
	// or
	$response = $client->user(1)->delete();
### Magic calls examples
	$client->getUser()					// GET /user
	$client->group(1)->getUser() 		// GET /group/1/user
	$client->group(1)->getUser(2)		// GET /group/1/user/2
	
	$client->postUser()				// POST /user
	$client->group(1)->postUser([]) 	// POST /group/1/user
### Authentication strategy	
	// anonymous auth
	$client = new Client(API_ENDPOINT);
	
	// basic auth
	use Rest\Client\Strategy\BasicAuthentication;
	
	$client = new Client(API_ENDPOINT, new BasicAuthentication(USER, PASSWORD));
	
	// client credentials
	use Rest\Client\Strategy\ClientCredentialsAuthentication;
	
	$client = new Client(API_ENDPOINT, new ClientCredentialsAuthentication(
		CLIENT_ID, 
		CLIENT_SECRET,
		AUTHENTICATION_URL
	)); 
## Server
### Create a server
	use Rest\Server\Server;

	$server = new Server();
	$server->run();
### Create a controller to handle requests
You must extend the abstract class Rest\Server\Controller
 
	use Rest\Server\Controller;
	
	class APIController extends Controller{
		/**
		 * @Route(/user/{id})
		 * @Method(GET)
		 */
		public function getUser(int $id, Request $request): Response{
			// ...
		}
	}

The routes will be created automatically according to the annotations @Route and @Method
Full example here : https://github.com/foxdie/rest/blob/master/Test/Controller/APIController.php
#### @Route annotation
- accepts regular expresssions
- accepts placeholders

#### @Method annotation
- possible values are GET, POST, PUT, PATCH and DELETE

### Register the controllers
	use Rest\Server\Server;

	$server = new Server();
	$server
		->registerController(new APIController());
		->registerController(...);
		->run();
### Create a middleware
You must implement the Rest\Server\Middleware interface

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
### Register the middlewares
	use Rest\Server\Server;

	$server = new Server();
	$server
		->addMiddleware(new BasicAuthenticationMiddleware())
		->addMiddleware(...)
		->registerController(new APIController())
		->run();
## OAuth2
### Client Credentials Grant
#### Client
	define("API_ENDPOINT", 		"http://rest/api/v2");
	define("AUTHENTICATION_URL", "http://rest/api/v2/token");
	define("CLIENT_ID", 			"...");
	define("CLIENT_SECRET", 		"...");
	
	use Rest\Client\Client;
	use Rest\Client\Strategy\ClientCredentialsAuthentication;
	use Rest\Response;
	
	$client = new Client(API_ENDPOINT, new ClientCredentialsAuthentication(
		CLIENT_ID, 
		CLIENT_SECRET,
		AUTHENTICATION_URL
	));
	
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
		->addClient(CLIENT_ID, CLIENT_SECRET);
	
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
