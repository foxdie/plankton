<?php

namespace Plankton\Client\Strategy;

use Plankton\Request;
use Plankton\Response;


class AnonymousAuthentication implements AuthenticationStrategy{
	/**
	 * {@inheritDoc}
	 * @see \Plankton\Client\Strategy\AuthenticationStrategy::send()
	 */
	public function send(Request $request, callable $requestCallback): ?Response{
		return $requestCallback($request);
	}
}
