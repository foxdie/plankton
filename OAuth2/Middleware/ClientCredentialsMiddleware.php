<?php

namespace OAuth2\Middleware;

use Rest\Server\Middleware;
use Rest\Server\RequestDispatcher;
use Rest\Request;
use Rest\Response;
use Rest\Exception;
use Oauth2\Provider\AccessTokenProvider;
use OAuth2\Grant\ClientCredentialsGrant;


class ClientCredentialsMiddleware implements Middleware{
	/**
	 * @access private
	 * @var AccessTokenProvider
	 */
	private $provider;
	
	/**
	 * @access public
	 * @param \AccessTokenProvider $provider
	 */
	public function __construct(AccessTokenProvider $provider){
		$this->provider = $provider;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Rest\RequestHandler::process()
	 */
	public function process(Request $request, RequestDispatcher $dispatcher): Response{
		// authorized request
		if ($request->getHeader("Authorization") && preg_match("#^.+ .+\$#", $request->getHeader("Authorization"))) {
			list($type, $token) = explode(" ", $request->getHeader("Authorization"));
			
			if (!$this->provider->isValidAccessToken(base64_decode($token))) { //invalid or expired token
				throw new Exception(ClientCredentialsGrant::ERROR_UNAUTHORIZED_CLIENT, 401);
			}
			
			return $dispatcher->process($request);
		}
		
		// grant request
		switch ($request->getData("grant_type")) {
			// access token request
			case ClientCredentialsGrant::GRANT_TYPE_CLIENT_CREDENTIALS:
				if (!$request->getData("client_id") || !$request->getData("client_secret")) {
					throw new Exception(ClientCredentialsGrant::ERROR_INVALID_REQUEST, 400);
				}
				
				break;
				
			// refresh token request
			case ClientCredentialsGrant::GRANT_TYPE_REFRESH_TOKEN:
				if (!$request->getData("refresh_token")) {
					throw new Exception(ClientCredentialsGrant::ERROR_INVALID_REQUEST, 400);
				}
				
				break;
				
			// unauthorized request
			default:
				throw new Exception(ClientCredentialsGrant::ERROR_UNSUPPORTED_GRANT_TYPE, 400);
				break;
		}

		return $dispatcher->process($request);
	}
}
