<?php

namespace Rest\Logging;

use Rest\Request;
use Rest\Response;


interface Logger{
	/**
	 * @access public
	 * @param Request $request
	 * @param Response $response
	 * @return void
	 */
	public function log(Request $request, Response $response = NULL): void;
}
