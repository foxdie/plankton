<?php

namespace Plankton\Client\Strategy;

use Plankton\Request;
use Plankton\Response;


interface AuthenticationStrategy{
	/**
	 * @abstract
	 * @param Request $request
	 * @param callable $requestCallback
	 * @return Response
	 */
	public function send(Request $request, callable $requestCallback): ?Response;
}
