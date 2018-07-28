

# Plankton: a RESTful API microframework

## Requirements

 - PHP 7.2
 - PHP cURL extension

## Installation

composer require foxdie/rest

## Table of content

## Client
### Create a client
	use Plankton\Client\Client;
		
	$client = new Client(API_ENDPOINT);
Full example here: https://github.com/foxdie/rest/blob/master/Test/public/client.php
### GET example
	$response = $client->get("/user");
#### using callback
    $client->get("/user", function(Response $response){
		echo $response;
	});
#### using magic
	$response = $client->getUser();
### POST example
	$response = $client->post("/user", ["email" => "foo@bar.com"]);
#### using callback
	$client->post("/user", ["email" => "foo@bar.com"], function(Response $response){
		echo $response->getLocation();
	});
#### using magic
	$response = $client->postUser(["email" => "foo@bar.com"]);
	// or
	$response = $client->user()->post(["email" => "foo@bar.com"]);
### PUT example
	$response = $client->put("/user/1", ["email" => "foo@bar.com"]);
#### using callback
	$client->put("/user/1", ["email" => "foo@bar.com"], function(Response $response){
		echo $response;
	});
#### using magic
	$response = $client->user(1)->put(["email" => "foo@bar.com"]);
### PATCH example
	$response = $client->patch("/user/1", ["email" => "foo@bar.com"]);
#### using callback	
	$client->patch("/user/1", ["email" => "foo@bar.com"], function(Response $response){
		echo $response;
	});
#### using magic
	$response = $client->user(1)->patch(["email" => "foo@bar.com"]);
### DELETE example
	$response = $client->delete("/user/1");
#### using callback
	$client->delete("/user/1", function(Response $response){
		echo $response;
	});
#### using magic
	$response = $client->deleteUser(1);
	// or
	$response = $client->user(1)->delete();
### Magic calls
#### Spinal case
If you want to use magic calls, your routes must use the spinal case
Example:

	$client->getUserAccount()
will match the following route:

	GET /user-account
Camel case and snake case are not supported
#### Examples
	$client->getUser();						// GET /user
	$client->group(1)->getUser(); 			// GET /group/1/user
	$client->group(1)->getUser(2);			// GET /group/1/user/2
	
	$client->postUser([])						// POST /user
	$client->group(1)->postUser([]) 			// POST /group/1/user
	
	$client->deleteUser(1);					// DELETE /user/1
	$client->user(1)->delete();				// DELETE /user/1
	$client->group(1)->deleteUser(2);		// DELETE /group/1/user/2
	$client->group(1)->user(2)->delete(); 	// DELETE /group/1/user/2
	$client->group(1)->user()->delete(2); 	// DELETE /group/1/user/2
### Authentication strategy	
#### anonymous auth
	$client = new Client(API_ENDPOINT);
	
#### basic auth
	use Plankton\Client\Strategy\BasicAuthentication;
	
	$client = new Client(API_ENDPOINT, new BasicAuthentication(USER, PASSWORD));
	
#### client credentials
	use Plankton\Client\Strategy\ClientCredentialsAuthentication;
	
	$client = new Client(API_ENDPOINT, new ClientCredentialsAuthentication(
		CLIENT_ID, 
		CLIENT_SECRET,
		AUTHENTICATION_URL
	)); 
The authorize and access/refresh token requests will be performed automatically.
The 3rd parameter is optionnal, the default value is "/token"
## Server
### Creating a server
	use Plankton\Server\Server;

	$server = new Server();
	$server->run();
### Handling requests
You must create a controller which extend the abstract class Plankton\Server\Controller
	
	use Plankton\Server\Controller;
	
	class APIController extends Controller{
	}
	
Your controller will contain one public method for each route of your API.
You can create routes in 2 different ways:
- using a config file
- using annotations

#### Using a config file
The routes are described in a YAML file
##### Example of config file
	routes:
	    get_users:
	        path: /user
	        method: GET
	        controller: Test\Controller\APIController::listUsers
	    create_user:
	        path: /user
	        method: POST
	        controller: Test\Controller\APIController::createUser
	        
Full example here: https://github.com/foxdie/plankton/blob/master/Test/config/server.yml

##### Configure the server 
	use Plankton\Server\{Server, Config};
	
	$server = new Server(new Config(CONFIG_PATH));
	$server->run();

Full example here: https://github.com/foxdie/plankton/blob/master/Test/public/config-server.php       
#### Using annotations
	use Plankton\Server\Controller;
	
	class APIController extends Controller{
		/**
		 * @Route(/user/{id})
		 * @Method(GET)
		 */
		public function getUser(int $id, Request $request): Response{
			// ...
		}
	}

The routes will be created automatically according to the annotations @Route and @Method.
Full example here : https://github.com/foxdie/rest/blob/master/Test/Controller/APIController.php
##### @Route annotation
- accepts regular expresssions
- accepts placeholders: they will be passed as argument in the same order as they appear
- the spinal case is strongly recommended

You can add a route prefix to your controller:
	
	/**
	 * @Route(/user)
	 */
	class APIController extends Controller{
		/**
		 * @Route(/{id})
		 * @Method(GET)
		 */
		public function getUser(int $id, Request $request): Response{
			// ...
		}
	}
##### @Method annotation
Possible values are:
- GET
- POST
- PUT
- PATCH
- DELETE

##### @Exception annotation
	class APIController extends Controller{
		/**
		 * This will catch any \CustomNameSpace\CustomException
		 * @Exception(CustomNameSpace\CustomException)
		 */
		public function catchCustomException(Exception $e, Request $request): Response{
		}
		
		/**
		 * This will catch all other exceptions
		 * @Exception(*)
		 */
		public function catchException(Exception $e, Request $request): Response{
		}
	}
### Registering controllers
	use Plankton\Server\Server;

	$server = new Server();
	$server
		->registerController(new APIController());
		->registerController(...);
		->run();
### Creating middlewares (optionnal)
You must implement the Plankton\Server\Middleware interface.
The middlewares can handle both incoming requests and outgoing responses.

	use Plankton\Server\{Request, Response};
	use Plankton\Server\{Middleware, RequestDispatcher};
	
	class BasicAuthenticationMiddleware implements Middleware{
		public function process(Request $request, RequestDispatcher $dispatcher): Response{
			// ...
			return $dispatcher->process($request);
		}
	}
Full example here: https://github.com/foxdie/rest/blob/master/Test/Middleware/BasicAuthenticationMiddleware.php
### Registering the middlewares
	use Plankton\Server\Server;

	$server = new Server();
	$server
		->addMiddleware(new BasicAuthenticationMiddleware())
		->addMiddleware(...)
		->registerController(new APIController())
		->run();
## OAuth2
### Client Credentials Grant
#### Client
	use Plankton\Client\Client;
	use Plankton\Client\Strategy\ClientCredentialsAuthentication;
	use Plankton\Response;
	
	$client = new Client(API_ENDPOINT, new ClientCredentialsAuthentication(
		CLIENT_ID, 
		CLIENT_SECRET,
		AUTHENTICATION_URL
	));

Full example here: 	
https://github.com/foxdie/rest/blob/master/Test/public/oauth2client.php
#### Server
	use Plankton\Server\Server;
	use OAuth2\Middleware\ClientCredentialsMiddleware;
	use OAuth2\Provider\MemoryProvider;
	use Test\Controller\APIController;
	
	// access token provider (we are using a simple memory provider for this example)
	$provider = new MemoryProvider();
	$provider->addClient(CLIENT_ID, CLIENT_SECRET);
	
	$server = new Server();
	$server
		->addMiddleware(new ClientCredentialsMiddleware($provider))
		->registerController(new APIController())
		->run();
Full example here:
https://github.com/foxdie/rest/blob/master/Test/public/oauth2server.php
##### Creating your own Access Token Provider
All you have to do is to implement the AccessTokenProvider interface:

	use Plankton\OAuth2\Provider\AccessTokenProvider;
	use Plankton\OAuth2\Token\{AccessToken, BearerToken};

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
