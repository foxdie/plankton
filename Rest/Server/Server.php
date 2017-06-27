<?php

namespace Rest\Server;


use Rest\Request;

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
			if ($controller->handleRequest($request)) {
				return;
			}
		}
	}
}
