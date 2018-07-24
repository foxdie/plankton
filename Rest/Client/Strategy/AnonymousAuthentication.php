<?php

namespace Rest\Client\Strategy;

use Rest\Request;
use Rest\Response;


class AnonymousAuthentication implements AuthenticationStrategy{
	/**
	 * {@inheritDoc}
	 * @see \Rest\Client\Strategy\AuthenticationStrategy::send()
	 */
	public function send(Request $request, callable $requestCallback): ?Response{
		return $requestCallback($request);
	}
}
