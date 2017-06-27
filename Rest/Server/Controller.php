<?php 

namespace Rest\Server;


use Rest\Route;
use ReflectionClass;
use Rest\Request;

abstract class Controller{
	/**
	 * @access private
	 * @var Route[]
	 */
	private $routes;
	
	/**
	 * @acces public
	 * @return string
	 */
	public function getName(){
		return get_class($this);
	}
	
	/**
	 * @access public
	 * @return \Rest\Route[]
	 */
	public function getRoutes(){
		if (!$this->routes) {
			$this->collectRoutes();
		}
		
		return $this->routes;
	}
	
	/**
	 * @access public
	 * @param \Rest\Request $request
	 * @return bool
	 */
	public function handleRequest(Request $request){
		$this->collectRoutes();
		
		foreach ($this->routes as $callable => $route) {
			if ($route->matchRequest($request)) {
				$args = $this->getPlaceholders($route, $request);
				call_user_func_array([$this, $callable], $args);
				
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * @access private
	 * @return void
	 */
	private function collectRoutes(){
		$this->routes = [];
	
		$rc = new ReflectionClass($this);
	
		foreach ($rc->getMethods() as $method) {
			if ($route = $this->getRouteFromMethod($method)) {
				$this->routes[$method->getName()] = $route;
			}
		}
	}
	
	/**
	 * @access private
	 * @param \ReflectionMethod $method
	 * @return bool|\Rest\Route
	 */
	private function getRouteFromMethod(\ReflectionMethod $method){
		if (!$method->isPublic() || method_exists("Rest\Server\Controller", $method->getName())) {
			return false;
		}
		
		$uri = false;
		$httpMethod = Request::METHOD_GET;
	
		//annotations
		$doc = $method->getDocComment();
		
		if ($doc && preg_match("/@Route[space]*\((.+)\)/i", $doc, $matches)) {
			$route = new Route(trim(str_replace(["'", "\""], "", $matches[1])));
				
			if (preg_match("/@method[space]*\((get|post|put|patch|delete)\)/i", $doc, $matches)) {
				$route->setMethod(strtoupper($matches[1]));
			}
			
			return $route;
		}
		
		//method
		if (preg_match("/^(get|post|put|path|delete)(.+)\$/", $method->getName(), $matches)) {
			$route = new Route("/" . $this->formatSegment($matches[2]));
			$route->setMethod(strtoupper($matches[1]));
			
			return $route;
		}
	
		return false;
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
	
	private function formatSegment($segment){
		return strtolower(preg_replace("/([a-z])([A-Z])/", "$1-$2", $segment));
	}
}
