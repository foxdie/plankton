<?php

namespace Rest\Server;


interface ControllerVisitor{
	/**
	 * @access public
	 * @param \Rest\Server\Controller $controller
	 * @return void
	 */
	public function visit(Controller $controller): void;
}
