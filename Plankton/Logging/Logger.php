<?php

namespace Plankton\Logging;

use Plankton\Request;
use Plankton\Response;


interface Logger{
	/**
	 * @access public
	 * @param Request $request
	 * @param Response $response
	 * @return void
	 */
	public function log(Request $request, Response $response = NULL): void;
}
