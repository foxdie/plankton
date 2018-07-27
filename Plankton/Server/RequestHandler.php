<?php

namespace Plankton\Server;

use Plankton\Request;
use Plankton\Response;


interface RequestHandler{
	/**
	 * @access public
	 * @param Request $request
	 * @param RequestDispatcher $dispatcher
	 * @return Response
	 */
	public function process(Request $request, RequestDispatcher $dispatcher): Response;
}
