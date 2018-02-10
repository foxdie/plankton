<?php

namespace OAuth2\Middleware;

use Rest\Server\Middleware;
use Rest\Server\RequestDispatcher;
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
		switch ($request->getParameter("grant_type")) {
			// access token request
			case ClientCredentialsGrant::GRANT_TYPE_CLIENT_CREDENTIALS:
				if (!$request->getParameter("client_id") || !$request->getParameter("client_secret")) {
					throw new AuthException(ClientCredentialsGrant::ERROR_INVALID_REQUEST, 400);
				}
				
				break;
				
			// refresh token request
			case ClientCredentialsGrant::GRANT_TYPE_REFRESH_TOKEN:
				if (!$request->getParameter("refresh_token")) {
					throw new AuthException(ClientCredentialsGrant::ERROR_INVALID_REQUEST, 400);
				}
				
				break;
				
			// unauthorized request
			default:
				throw new AuthException(ClientCredentialsGrant::ERROR_UNSUPPORTED_GRANT_TYPE, 400);
				break;
		}

		return $dispatcher->process($request);
	}
}
