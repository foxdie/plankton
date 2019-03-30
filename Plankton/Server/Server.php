<?php

namespace Plankton\Server;

use Plankton\Exception;
use Plankton\Request;
use Plankton\Response;


class Server implements RequestHandler{
	/**
	 * @access protected
	 * @var Controller[]
	 */
	protected $controllers;
	
	/**
	 * @access protected
	 * @var \Plankton\Request
	 */
	protected $request;
	
	/**
	 * @access protected
	 * @var \Plankton\Server\ControllerVisitor[]
	 */
	protected $visitors;
	
	/**
	 * @access protected
	 * @var Middleware[]
	 */
	protected $middlewares;
	
	/**
	 * @access protected
	 * @var Config
	 */
	protected $config;
	
	/**
	 * @access public
	 * @return void
	 */
	public function __construct(Config $config = null){
		$this->controllers = [];
		$this->middlewares = [];
		$this->config = $config;
		
		$this->request = $this->buildRequest();
		$this->visitors = $config ? [] : [new RouteVisitor(), new ExceptionVisitor()];
	}
	
	/**
	 * @access public
	 * @return Server
	 */
	public function registerController(Controller $controller): Server{
		foreach ($this->visitors as $visitor) {
			$controller->accept($visitor);
		}
		
		$this->controllers[$controller->getName()] = $controller;
		
		return $this;
	}

	/**
	 * @acces public
	 * @param Middleware $middleware
	 * @return Server
	 */
	public function addMiddleware(Middleware $middleware): Server{
		$this->middlewares[] = $middleware;
		
		return $this;
	}
	
	/**
	 * @access public
	 * @throws \RuntimeException
	 * @return void
	 */
	public function run(): void{
		$this->configure();
		
		try{
			$dispatcher = new RequestDispatcher();
			
			foreach ($this->middlewares as $middleware) {
				$dispatcher->pipe($middleware);	
			}
			
			$dispatcher->pipe($this);
			$this->send($dispatcher->process($this->request));
		}
		catch (Exception $e) {
			$this->handleException($e);
		}
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Plankton\RequestHandler::process()
	 */
	public function process(Request $request, RequestDispatcher $dispatcher): Response{
		foreach ($this->controllers as $controller) {
			if ($response = $controller->handleRequest($request)) {
				return $response;
			}
		}
		
		throw new Exception("Not Found", 404);
	}
	
	/**
	 * @access protected
	 * @param \Plankton\Response $response
	 * @throws \RuntimeException
	 * @return void
	 */
	protected function send(Response $response): void{
		if (headers_sent()) {
			throw new \RuntimeException("headers already sent");
		}
		
		http_response_code($response->getCode());

		foreach ($response->getHeaders() as $name => $value) {
			header($name . ": " . $value);	
		}
		
		echo $response;
	}
	
	/**
	 * @access protected
	 * @return \Plankton\Request
	 */
	protected function buildRequest(): Request{
		$uri = $_SERVER["PATH_INFO"] ?? preg_replace("/^(.+)\?.*\$/", "\$1", $_SERVER["REQUEST_URI"]);

		$url = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443)?'https':'http' . "://" . $_SERVER["HTTP_HOST"] . $uri;
		
		$request = new Request($url, $_SERVER["REQUEST_METHOD"]);
		
		parse_str(isset($_SERVER["QUERY_STRING"])?$_SERVER['QUERY_STRING']:'', $parameters);
		foreach ($parameters as $name => $value) {
			$request->setParameter($name, $value);
		}
		
		foreach (getallheaders() as $name => $value) {
			$request->setHeader($name, $value);
		}

		$d = file_get_contents("php://input");

		if ($request->getHeader('Content-Type') == Response::CONTENT_TYPE_JSON) {
			$data = json_decode($d, true);
		} else {
			parse_str($d, $data);
		}

		$request->setData($data);

		return $request;
	}
	
	/**
	 * @access protected
	 * @param \Plankton\Exception $e
	 * @throws \RuntimeException
	 * @return void
	 */
	protected function handleException(Exception $e): void{
		foreach ($this->controllers as $controller) {
			if ($ret = $controller->handleException($e, $this->request)) {
				if ($ret instanceof Response) {
					$this->send($ret);
				}
					
				return;
			}
		}
		
		// exception is not handled by any controller
		$response = new Response();
		
		$response
			->setCode($e->getCode())
			->setContent($e->getMessage());
	
		$this->send($response);
	}
	
	protected function configure(){
		if (!$this->config) {
			return;
		}
	
		foreach ($this->config->getControllers() as $controller) {
			$this->registerController($controller);
		}
		
		foreach ($this->controllers as $controller) {
			$this->config->applyTo($controller);
		}
	}
}
