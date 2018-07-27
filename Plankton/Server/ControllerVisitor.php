<?php

namespace Plankton\Server;


interface ControllerVisitor{
	/**
	 * @access public
	 * @param \Plankton\Server\Controller $controller
	 * @return void
	 */
	public function visit(Controller $controller): void;
}
