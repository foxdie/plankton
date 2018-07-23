<?php 

namespace Rest\Server;


use Rest\Server\Route;
use Rest\Request;
use Rest\Response;
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
	 * @access public
	 * @param \Rest\Server\ControllerVisitor $visitor
	 * @return void
	 */
	public function accept(ControllerVisitor $visitor): void{
		$visitor->visit($this);
	}
	
	/**
	 * @acces public
	 * @return string
	 */
	public function getName(): string{
		return get_class($this);
	}
	
	/**
	 * @access public
	 * @return \SplObjectStorage
	 */
	public function getRoutes(): \SplObjectStorage{
		return $this->routes;
	}
	
	/**
	 * @acccess public
	 * @return \SplObjectStorage
	 */
	public function getExceptionHandlers(): \SplObjectStorage{
		return $this->exceptionHandlers;
	}
	
	/**
	 * @access public
	 * @param \Rest\Request $request
	 * @return Response|bool
	 */
	public function handleRequest(Request $request){
		if (!$this->routes) {
			$this->routes = new \SplObjectStorage();
		}
		
		foreach ($this->routes as $route) {
			if ($route->matchRequest($request)) {
				$args = $this->getPlaceholders($route, $request) ?: [];
				$args[] = $request;
				
				$callable = $this->routes[$route];
				$ret = call_user_func_array($callable, $args);
				
				return $ret instanceof Response ? $ret : true;
			}
		}
		
		//@todo throw exception?
		return false;
	}
	
	/**
	 * @access public
	 * @param \Rest\Exception $e
	 * @param \Rest\Request $request
	 * @return mixed
	 */
	public function handleException(Exception $e, Request $request): ?Response{
		if (!$this->exceptionHandlers) {
			return null;
		}
		
		foreach ($this->exceptionHandlers as $exception => $handler) {
			if (get_class($e) == "Rest\\{$exception}" || $exception == "*") { //@todo remove namespace limitation
				$handler = $this->exceptionHandlers[$exception];
				$ret = call_user_func_array($handler, [$e, $request]);
	
				return $ret instanceof Response ? $ret : true;
			}
		}
	
		return null;
	}
	
	/**
	 * @access public
	 * @param \Rest\Server\Route $route
	 * @param callable $callable
	 * @return Controller
	 */
	public function addRoute(Route $route, callable $callable): Controller{
		if (!$this->routes) {
			$this->routes = new \SplObjectStorage();
		}
		
		$this->routes[$route] = $callable;
		
		return $this;
	}
	
	/**
	 * @access public
	 * @param string $exception
	 * @param callable $callable
	 * @return Controller
	 */
	public function addExceptionHandler($exception, callable $callable): Controller{
		if (!$this->exceptionHandlers) {
			$this->exceptionHandlers = [];
		}
	
		$this->exceptionHandlers[$exception] = $callable;
		
		return $this;
	}
	
	/**
	 * @access private
	 * @param Route $route
	 * @param Request $request
	 * @return array|null
	 */
	private function getPlaceholders(Route $route, Request $request): ?array{
		if (!preg_match_all("#{([^}]+)}#", $route->getURI(), $matches)) {
			return null;
		}
		
		$placeholders = [];
		$regexp = "#" . $route->getURI() . "#";
		foreach ($matches[1] as $placeholder) {
			// capture placeholder
			$regexp = str_replace("{" . $placeholder . "}", "([^/]+)", $regexp);
			$placeholders[] = $placeholder;
			
		}

		if (!preg_match_all($regexp, $request->getURI(), $matches)) {
			return null;
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
	private function formatSegment(string $segment): string{
		return strtolower(preg_replace("/([a-z])([A-Z])/", "$1-$2", $segment));
	}
}
