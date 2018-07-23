<?php

namespace OAuth2\Controller;

use Rest\Request;
use Rest\Response;
use Rest\Server\Controller;
use Rest\Exception;
use OAuth2\Provider\AccessTokenProvider;
use OAuth2\Grant\ClientCredentialsGrant;


class AccessTokenController extends Controller{
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
	 * @Route(/token)
	 * @Method(POST)
	 */
	public function createAccessToken(Request $request): Response{
		if (!$request->getData("client_id") || !$request->getData("client_secret")) {
			throw new Exception(ClientCredentialsGrant::ERROR_INVALID_REQUEST, 400);
		}
		
		$token = $this->provider->getAccessToken($request->getData("client_id"), $request->getData("client_secret"));
		
		if (!$token) {
			throw new Exception(ClientCredentialsGrant::ERROR_INVALID_CLIENT, 401);
		}
		
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

