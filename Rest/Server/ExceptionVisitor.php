<?php


namespace Rest\Server;


class ExceptionVisitor implements ControllerVisitor{
	/**
	 * {@inheritDoc}
	 * @see \Rest\Server\ControllerVisitor::visit()
	 */
	public function visit(Controller $controller): void{
		$this->collectExceptionHandlers($controller, new \ReflectionClass($controller));
	}
	
	/**
	 * @access public
	 * @param \Rest\Server\Controller $controller
	 * @param \ReflectionClass $rc
	 * @return void
	 */
	public function collectExceptionHandlers(Controller $controller, \ReflectionClass $rc): void{
		foreach ($rc->getMethods() as $method) {
			if ($exception = $this->getHandledExceptionFromMethod($method)) {
				$controller->addExceptionHandler($exception, [$controller, $method->getName()]);
			}
		}
	}
	
	/**
	 * @access private
	 * @param \ReflectionMethod $method
	 * @return string|null
	 */
	private function getHandledExceptionFromMethod(\ReflectionMethod $method): ?string{
		if (!$method->isPublic() || method_exists("Rest\Server\Controller", $method->getName())) {
			return null;
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
	
		return null;
	}
}
