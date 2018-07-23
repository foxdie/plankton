<?php

namespace Rest\Server;

use Rest\Exception;
use Rest\NotFoundException;
use Rest\Request;
use Rest\Response;


class Server implements RequestHandler{
	/**
	 * @access protected
	 * @var Controller[]
	 */
	protected $controllers;
	
	/**
	 * @access protected
	 * @var \Rest\Request
	 */
	protected $request;
	
	/**
	 * @access protected
	 * @var \Rest\Server\ControllerVisitor[]
	 */
	protected $visitors;
	
	/**
	 * @access protected
	 * @var Middleware[]
	 */
	protected $middlewares;
	
	/**
	 * @access public
	 * @return void
	 */
	public function __construct(){
		$this->controllers = [];
		$this->middlewares = [];
		
		$this->request = $this->buildRequest();
		$this->visitors = [new RouteVisitor(), new ExceptionVisitor()];
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
	 * @see \Rest\RequestHandler::process()
	 */
	public function process(Request $request, RequestDispatcher $dispatcher): Response{
		foreach ($this->controllers as $controller) {
			if ($response = $controller->handleRequest($request)) {
				return $response;
			}
		}
		
		throw new NotFoundException();
	}
	
	/**
	 * @access protected
	 * @param \Rest\Response $response
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
	 * @return \Rest\Request
	 */
	protected function buildRequest(): Request{
		$uri = $_SERVER["PATH_INFO"] ?? preg_replace("/^(.+)\?.*\$/", "\$1", $_SERVER["REQUEST_URI"]);
		$url = $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . $uri;
		
		$request = new Request($url, $_SERVER["REQUEST_METHOD"]);
		
		parse_str(file_get_contents("php://input"), $data);
		$request->setData($data);
		
		parse_str($_SERVER["QUERY_STRING"], $parameters);
		foreach ($parameters as $name => $value) {
			$request->setParameter($name, $value);
		}
		
		foreach (getallheaders() as $name => $value) {
			$request->setHeader($name, $value);
		}
		
		return $request;
	}
	
	/**
	 * @access protected
	 * @param \Rest\Exception $e
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
}
