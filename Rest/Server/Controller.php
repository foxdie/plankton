<?php 

namespace Rest\Server;


use Rest\Server\Route;
use Rest\Server\Request;
use Rest\Server\Response;
use Rest\Exception;

abstract class Controller{
	/**
	 * @access private
	 * @var \SplObjectStorage
	 */
	private $routes;
	
	/**
	 * @access private
	 * @var \SplObjectStorage
	 */
	private $exceptionHandlers;
	
	/**
	 * @acces public
	 * @return string
	 */
	public function getName(){
		return get_class($this);
	}
	
	/**
	 * @access public
	 * @return \SplObjectStorage
	 */
	public function getRoutes(){
		if (!$this->routes) {
			ControllerFactory::collectRoutes($this);
		}
		
		return $this->routes;
	}
	
	/**
	 * @acccess public
	 * @return \SplObjectStorage
	 */
	public function getExceptionHandlers(){
		if (!$this->exceptionHandlers) {
			ControllerFactory::collectExceptions($this);
		}
	
		return $this->exceptionHandlers;
	}
	
	/**
	 * @access public
	 * @param \Rest\Request $request
	 * @return \Rest\Server\Response|bool
	 */
	public function handleRequest(Request $request){
		ControllerFactory::collectRoutes($this);
		
		foreach ($this->routes as $route) {
			if ($route->matchRequest($request)) {
				$args = $this->getPlaceholders($route, $request) ?: [];
				$args[] = $request;
				
				$callable = $this->routes[$route];
				$ret = call_user_func_array($callable, $args);
				
				return $ret instanceof Response ? $ret : true;
			}
		}
		
		return false;
	}
	
	/**
	 * @access public
	 * @param \Rest\Exception $e
	 * @param \Rest\Server\Request $request
	 * @return \Rest\Server\Response|bool
	 */
	public function handleException(Exception $e, Request $request){
		ControllerFactory::collectExceptionHandlers($this);
	
		foreach ($this->exceptionHandlers as $exception => $handler) {
			if (get_class($e) == "Rest\\{$exception}" || $exception == "*") {
				$handler = $this->exceptionHandlers[$exception];
				$ret = call_user_func_array($handler, [$e, $request]);
	
				return $ret instanceof Response ? $ret : true;
			}
		}
	
		return false;
	}
	
	/**
	 * @access public
	 * @param \Rest\Server\Route $route
	 * @param callable $callable
	 * @return void
	 */
	public function addRoute(Route $route, callable $callable){
		if (!$this->routes) {
			$this->routes = new \SplObjectStorage();
		}
		
		$this->routes[$route] = $callable;
	}
	
	/**
	 * @access public
	 * @param string $exception
	 * @param callable $callable
	 * @return void
	 */
	public function addExceptionHandler($exception, callable $callable){
		if (!$this->exceptionHandlers) {
			$this->exceptionHandlers = [];
		}
	
		$this->exceptionHandlers[$exception] = $callable;
	}
	
	/**
	 * @access private
	 * @param Route $route
	 * @param Request $request
	 * @return array|bool
	 */
	private function getPlaceholders(Route $route, Request $request){
		if (!preg_match_all("#{([^}]+)}#", $route->getURI(), $matches)) {
			return false;
		}
		
		$placeholders = [];
		$regexp = "#" . $route->getURI() . "#";
		foreach ($matches[1] as $placeholder) {
			//capture placeholder
			$regexp = str_replace("{" . $placeholder . "}", "([^/]+)", $regexp);
			$placeholders[] = $placeholder;
			
		}

		if (!preg_match_all($regexp, $request->getURI(), $matches)) {
			return false;
		}

		$ret = [];
		foreach ($placeholders as $i => $placeholder) {
			$ret[$placeholder] = $matches[$i + 1][0];
		}
		
		return $ret;
	}

	/**
	 * @access private
	 * @param string $segment
	 * @return string
	 */
	private function formatSegment($segment){
		return strtolower(preg_replace("/([a-z])([A-Z])/", "$1-$2", $segment));
	}
}
