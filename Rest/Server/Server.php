<?php

namespace Rest\Server;


use Rest\Exception;
use Rest\NotFoundException;

class Server implements RequestHandler{
	/**
	 * @access protected
	 * @var Controller[]
	 */
	protected $controllers;
	
	/**
	 * @access protected
	 * @var \Rest\Server\Request
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
		
		$this->request = new Request();
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
	 * @see \Rest\Server\RequestHandler::process()
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
	 * @param \Rest\Server\Response $response
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
	}
}
