<?php

namespace OAuth2\Middleware;

use Rest\Server\Middleware;
use Rest\Server\RequestDispatcher;
use Rest\Request;
use Rest\Response;
use OAuth2\Provider\AccessTokenProvider;
use OAuth2\Grant\ClientCredentialsGrant;
use OAuth2\Token\AccessToken;


class ClientCredentialsMiddleware implements Middleware{
	/**
	 * @access private
	 * @var AccessTokenProvider
	 */
	private $provider;
	
	/**
	 * @access private
	 * @var string
	 */
	private $tokenURI;
	
	/**
	 * @access public
	 * @param \AccessTokenProvider $provider
	 */
	public function __construct(AccessTokenProvider $provider, $tokenURI = "/token"){
		$this->provider = $provider;
		$this->tokenURI = $tokenURI;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Rest\RequestHandler::process()
	 */
	public function process(Request $request, RequestDispatcher $dispatcher): Response{
		// authorized request
		if ($request->getHeader("Authorization") && preg_match("#^Bearer .+\$#", $request->getHeader("Authorization"))) {
			return $this->processAuthorizedRequest($request, $dispatcher);
		}
		
		// grant request
		if ($request->getMethod() == Request::METHOD_POST && $request->getURI() == $this->tokenURI) {
			switch ($request->getData("grant_type")) {
				// access token request
				case ClientCredentialsGrant::GRANT_TYPE_CLIENT_CREDENTIALS:
					return $this->processAccessTokenRequest($request, $dispatcher);
				
				// refresh token request
				case ClientCredentialsGrant::GRANT_TYPE_REFRESH_TOKEN:
					return $this->processRefreshtRequest($request, $dispatcher);
				
				// bad request
				default:
					throw new Exception(ClientCredentialsGrant::ERROR_UNSUPPORTED_GRANT_TYPE, 400);
			}
		}
		
		// unauthorized request
		throw new Exception(ClientCredentialsGrant::ERROR_UNAUTHORIZED_CLIENT, 401);
	}
	
	/**
	 * @access private
	 * @param Request $request
	 * @param RequestDispatcher $dispatcher
	 * @throws Exception
	 * @return Response
	 */
	private function processAuthorizedRequest(Request $request, RequestDispatcher $dispatcher): Response{
		list($type, $token) = explode(" ", $request->getHeader("Authorization"));
		
		if (!$this->provider->isValidAccessToken(base64_decode($token))) { //invalid or expired token
			throw new Exception(ClientCredentialsGrant::ERROR_UNAUTHORIZED_CLIENT, 401);
		}
		
		return $dispatcher->process($request);
	}
	
	/**
	 * @access private
	 * @param Request $request
	 * @param RequestDispatcher $dispatcher
	 * @throws Exception
	 * @return Response
	 */
	private function processAccessTokenRequest(Request $request, RequestDispatcher $dispatcher): Response{
		if (!$request->getData("client_id") || !$request->getData("client_secret")) {
			throw new Exception(ClientCredentialsGrant::ERROR_INVALID_REQUEST, 400);
		}
		
		if ($token = $this->provider->getAccessToken($request->getData("client_id"), $request->getData("client_secret"))) {
			return $this->createTokenResponse($token);
		}
		
		throw new Exception(ClientCredentialsGrant::ERROR_UNAUTHORIZED_CLIENT, 401);
	}
	
	/**
	 * @access private
	 * @param Request $request
	 * @param RequestDispatcher $dispatcher
	 * @throws Exception
	 * @return Response
	 */
	private function processRefreshsTokenRequest(Request $request, RequestDispatcher $dispatcher): Response{
		if (!$request->getData("refresh_token")) {
			throw new Exception(ClientCredentialsGrant::ERROR_INVALID_REQUEST, 400);
		}

		if ($token = $this->provider->refreshToken(base64_decode($request->getData("refresh_token")))) {
			return $this->createTokenResponse($token);
		}
		
		throw new Exception(ClientCredentialsGrant::ERROR_UNAUTHORIZED_CLIENT, 401);
	}
	
	/**
	 * @access private
	 * @param AccessToken $token
	 * @return Response
	 */
	private function createTokenResponse(AccessToken $token): Response{
		$response = new Response();
		
		$data = [
			"access_token" 	=> base64_encode($token->getValue()),
			"token_type" 	=> $token->getType(),
			"expires_in" 	=> $token->getExpiration(),
			"refresh_token" => base64_encode($token->getRefreshToken()),
			"scope"			=> $token->getScopes()
		];
		
		$response
			->setCode(200)
			->setContent($data)
			->setHeader("Cache-Control", "no-store")
			->setHeader("Pragma", "no-cache");
		
		return $response;
	}
}
