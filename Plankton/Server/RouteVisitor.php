<?php


namespace Plankton\Server;

use Plankton\Request;


class RouteVisitor implements ControllerVisitor{
	/**
	 * @access private
	 * @var string
	 */
	private $prefix;
	
	/**
	 * {@inheritDoc}
	 * @see \Plankton\Server\ControllerVisitor::visit()
	 */
	public function visit(Controller $controller): void{
		$rc = new \ReflectionClass($controller);
		
		$this->prefix = $this->getRoutesPrefix($rc);
		$this->collectRoutes($controller, $rc);
	}
	
	/**
	 * @access public
	 * @param \Plankton\Server\Controller $controller
	 * @param \ReflectionClass $rc
	 * @return void
	 */
	public function collectRoutes(Controller $controller, \ReflectionClass $rc): void{
		foreach ($rc->getMethods() as $method) {
			if ($route = $this->getRouteFromMethod($method)) {
				$controller->addRoute($route, [$controller, $method->getName()]);
			}
		}
	}
	
	/**
	 * @access private
	 * @param \ReflectionClass $rc
	 * @return string
	 */
	private function getRoutesPrefix(\ReflectionClass $rc): string{
		$doc = $rc->getDocComment();
		
		if ($doc && preg_match("/@Route[space]*\((.+)\)/i", $doc, $matches)) {
			return $matches[1];
		}
		
		return "/";
	}
	
	/**
	 * @access private
	 * @param \ReflectionMethod $method
	 * @return \Plankton\Server\Route|null
	 */
	private function getRouteFromMethod(\ReflectionMethod $method): ?Route{
		if (!$method->isPublic() || method_exists("Plankton\Server\Controller", $method->getName())) {
			return null;
		}
		
		// annotations
		$doc = $method->getDocComment();
	
		if ($doc && preg_match("/@Route[space]*\((.+)\)/i", $doc, $matches)) {
			$route = new Route($this->sanitizeURI($this->prefix . $matches[1]));
	
			if (preg_match("/@method[space]*\(.*(get|post|put|patch|delete).*\)/i", $doc, $matches)) {
				$route->setMethod(strtoupper(trim(str_replace(["'", "\""], "", $matches[1]))));
			}
	
			return $route;
		}
	
		// method
		if (preg_match("/^(get|post|put|path|delete)(.+)\$/", $method->getName(), $matches)) {
			$route = new Route("/" . $this->formatSegment($matches[2]));
			$route->setMethod(strtoupper($matches[1]));
	
			return $route;
		}
	
		return null;
	}
	
	/**
	 * @access private
	 * @param string $uri
	 * @return string
	 */
	private function sanitizeURI(string $uri): string{
		$uri = trim(str_replace(["'", "\""], "", $uri));
		$uri = str_replace("//", "/", $uri);
		
		if (strlen($uri) > 1 && $uri[-1] == "/") {
			$uri = substr($uri, 0, -1);
		}
		
		return $uri;
	}
}
