<?php


namespace Rest\Server;

use Rest\Request;


class RouteVisitor implements ControllerVisitor{
	/**
	 * {@inheritDoc}
	 * @see \Rest\Server\ControllerVisitor::visit()
	 */
	public function visit(Controller $controller): void{
		$this->collectRoutes($controller, new \ReflectionClass($controller));
	}
	
	/**
	 * @access public
	 * @param \Rest\Server\Controller $controller
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
	 * @param \ReflectionMethod $method
	 * @return \Rest\Server\Route|null
	 */
	private function getRouteFromMethod(\ReflectionMethod $method): ?Route{
		if (!$method->isPublic() || method_exists("Rest\Server\Controller", $method->getName())) {
			return null;
		}
	
		$uri = false;
		$httpMethod = Request::METHOD_GET;
	
		// annotations
		$doc = $method->getDocComment();
	
		if ($doc && preg_match("/@Route[space]*\((.+)\)/i", $doc, $matches)) {
			$route = new Route(trim(str_replace(["'", "\""], "", $matches[1])));
	
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
}
