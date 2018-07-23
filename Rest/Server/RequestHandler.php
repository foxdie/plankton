<?php

namespace Rest\Server;

use Rest\Request;
use Rest\Response;


interface RequestHandler{
	/**
	 * @access public
	 * @param Request $request
	 * @param RequestDispatcher $dispatcher
	 * @return Response
	 */
	public function process(Request $request, RequestDispatcher $dispatcher): Response;
}
