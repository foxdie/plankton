<?php

namespace OAuth2\Middleware;

use Rest\Server\Middleware;
use Rest\RequestDispatcher;
use OAuth2\Grant\ClientCredentialsGrant;
use OAuth2\AuthException;
use Rest\Request;
use Rest\Response;


class ClientCredentialsMiddleware implements Middleware{
	/**
	 * {@inheritDoc}
	 * @see \Rest\RequestHandler::process()
	 */
	public function process(Request $request, RequestDispatcher $dispatcher): Response{
		if( $request->getHeader("grant_type") != "client_credentials") {
			throw new AuthException(ClientCredentialsGrant::ERROR_UNSUPPORTED_GRANT_TYPE, 400);
		}
		
		if (!$request->hasHeader("client_id") || !$request->hasHeader("client_secret")) {
			throw new AuthException(ClientCredentialsGrant::ERROR_INVALID_REQUEST, 400);
		}
		
		return $dispatcher->process($request);
	}
}
