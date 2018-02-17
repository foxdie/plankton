<?php

namespace Rest\Client\Auth;

use Rest\Request;
use Rest\Response;


class AnonymousAuthentication implements AuthenticationStrategy{
	/**
	 * {@inheritDoc}
	 * @see \Rest\Client\Auth\AuthenticationStrategy::send()
	 */
	public function send(Request $request, callable $requestCallback): ?Response{
		return $requestCallback($request);
	}
}
