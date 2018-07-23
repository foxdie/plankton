

# RESTful API microframework

## Requirements

 - PHP 7.2
 - PHP cURL extension

## Installation

composer require foxdie/rest

### Client
#### Create a client
	use Rest\Client\Client;
	use Rest\Client\Response;
	
	$client = new Client("http://rest/api/v1");
Full example here: https://github.com/foxdie/rest/blob/master/Test/public/client.php
#### GET example
    $client->get("/user", function(Response $response){
		echo $response;
	});
#### POST example
	$client->post("/user", ["email" => "foo@bar.com"], function(Response $response){
		echo $response->getLocation();
	});
#### PUT example
	$client->put("/user/1", ["email" => "foo@bar.com"], function(Response $response){
		echo $response;
	});
#### PATCH example
	$client->patch("/user/1", ["email" => "foo@bar.com"], function(Response $response){
		echo $response;
	});
#### DELETE example
	$client->delete("/user/1", function(Response $response){
		echo $response;
	});
### Server
#### Create a server
	use Rest\Server\Server;

	$server = new Server();
	$server->run();
#### Create a controller to handle requests
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
#### Register the controller
	use Rest\Server\Server;

	$server = new Server();
	$server
		->registerController(new APIController());
		->run();
#### Create a middleware
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
#### Register the middleware
	use Rest\Server\Server;

	$server = new Server();
	$server
		->addMiddleware(new BasicAuthenticationMiddleware())
		->registerController(new APIController())
		->run();
