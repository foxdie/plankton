<?php

namespace Rest\Client\Auth;

use Rest\Request;
use Rest\Response;


interface AuthenticationStrategy{
	/**
	 * @abstract
	 * @param Request $request
	 * @param callable $requestCallback
	 * @return Response
	 */
	public function send(Request $request, callable $requestCallback): ?Response;
}
