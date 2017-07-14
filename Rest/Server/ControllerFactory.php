<?php

namespace Rest\Server;


use Rest\Server\Controller;
use Rest\Server\Request;
use Rest\Route;

class ControllerFactory {
	/**
	 * @access private
	 * @static
	 * @var \ReflectionClass[]
	 */
	private static $reflectionClasses = [];
	
	/**
	 * @access public
	 * @static
	 * @return void
	 */
	public static function collectRoutes(Controller $controller){
		$rc = self::getReflectionClass($controller);
		
		foreach ($rc->getMethods() as $method) {
			if ($route = self::getRouteFromMethod($method)) {
				$controller->addRoute($route, [$controller, $method->getName()]);
			}
		}
	}
	
	/**
	 * @access public
	 * @static
	 * @param \Rest\Server\Controller $controller
	 * @return void
	 */
	public static function collectExceptionHandlers(Controller $controller){
		$rc = self::getReflectionClass($controller);
	
		foreach ($rc->getMethods() as $method) {
			if ($exception = self::getHandledExceptionFromMethod($method)) {
				$controller->addExceptionHandler($exception, [$controller, $method->getName()]);
			}
		}
	}
	
	/**
	 * @access private
	 * @static
	 * @param \ReflectionMethod $method
	 * @return bool|\Rest\Route
	 */
	private static function getRouteFromMethod(\ReflectionMethod $method){
		if (!$method->isPublic() || method_exists("Rest\Server\Controller", $method->getName())) {
			return false;
		}
	
		$uri = false;
		$httpMethod = Request::METHOD_GET;
	
		//annotations
		$doc = $method->getDocComment();
	
		if ($doc && preg_match("/@Route[space]*\((.+)\)/i", $doc, $matches)) {
			$route = new Route(trim(str_replace(["'", "\""], "", $matches[1])));
	
			if (preg_match("/@method[space]*\(.*(get|post|put|patch|delete).*\)/i", $doc, $matches)) {
				$route->setMethod(strtoupper(trim(str_replace(["'", "\""], "", $matches[1]))));
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
	 * @static
	 * @param \ReflectionMethod $method
	 * @return boolean|string
	 */
	private static function getHandledExceptionFromMethod(\ReflectionMethod $method){
		if (!$method->isPublic() || method_exists("Rest\Server\Controller", $method->getName())) {
			return false;
		}
	
		//annotations
		$doc = $method->getDocComment();
	
		if ($doc && preg_match("/@Exception[space]*\((.+)\)/i", $doc, $matches)) {
			return trim($matches[1]);
		}
	
		//method
		if (preg_match("/^(.+)Exception\$/i", $method->getName(), $matches)) {
			return trim($matches[1]);
		}
	
		return false;
	}
	
	/**
	 * @access private
	 * @static
	 * @param \Rest\Server\Controller $controller
	 * @return \ReflectionClass
	 */
	private static function getReflectionClass(Controller $controller){
		$className = get_class($controller);
		
		if (!isset(self::$reflectionClasses[$className])) {
			self::$reflectionClasses[$className] = new \ReflectionClass($controller);
		}
		
		return self::$reflectionClasses[$className];
	}
}
