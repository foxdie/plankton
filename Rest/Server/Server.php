<?php

namespace Rest\Server;


use Rest\Request;
use Rest\Response;

class Server{
	/**
	 * @access protected
	 * @var Controller[]
	 */
	protected $controllers;
	
	/**
	 * @access public
	 * @return void
	 */
	public function __construct(){
		$this->controllers = [];
	}
	
	/**
	 * @access public
	 * @return Server
	 */
	public function registerController(Controller $controller){
		$this->controllers[$controller->getName()] = $controller;
		
		return $this;
	}

	/**
	 * @access public
	 * @throws \RuntimeException
	 * @return void
	 */
	public function run(){
		$request = new Request();

		foreach ($this->controllers as $controller) {
			if ($ret = $controller->handleRequest($request)) {
				if ($ret instanceof Response) {
					$this->respond($ret);	
				}
				
				return;
			}
		}
	}
	
	/**
	 * @access protected
	 * @param \Rest\Response $response
	 * @throws \RuntimeException
	 * @return void
	 */
	protected function respond(Response $response){
		if (headers_sent()) {
			throw new \RuntimeException("headers already sent");
		}
		
		http_response_code($response->getCode());

		foreach ($response->getHeaders() as $name => $value) {
			header($name . ": " . $value);	
		}
		
		echo $response;
	}
}
