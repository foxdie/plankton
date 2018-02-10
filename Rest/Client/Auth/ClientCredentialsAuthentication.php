<?php

namespace Rest\Client\Auth;

use Rest\Request;
use Rest\Response;
use OAuth2\Grant\ClientCredentialsGrant;
use OAuth2\Token\BearerToken;


class ClientCredentialsAuthentication extends AuthenticationStrategy{
	/**
	 * @access private
	 * @var string
	 */
	private $client_id;
	
	/**
	 * @access private
	 * @var string
	 */
	private $client_secret;
	
	/**
	 * @access private
	 * @var OAuth2\Token\BearerToken
	 */
	private $accessToken;
	
	/**
	 * @access private
	 * @var string$grant
	 */
	private $authorizationURL;
	
	/**
	 * @access private
	 * @var string[]
	 */
	private $scopes;
	
	/**
	 * @param string $client_id
	 * @param string $client_secret
	 * @param string $authorizationURL
	 * @param string[] $scopes
	 */
	public function __construct(string $client_id, string $client_secret, string $authorizationURL, array $scopes = []){
		$this->client_id 		= $client_id;
		$this->client_secret 	= $client_secret;
		$this->authorizationURL = $authorizationURL;
		$this->scopes			= $scopes;
		$this->accessToken 		= NULL;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Rest\Client\Auth\AuthenticationStrategy::send()
	 */
	public function send(Request $request): ?Response{
		if (!$this->accessToken && !$request->hasParameter("grant_type")) {
			$response = $this->requestAccessToken();
		}
		
		// @todo test response or token
		// @todo if token has expired, refresh token
		
		$request->setHeader(
			"Authorization", 
			"Bearer " . base64_encode($this->accessToken)
		);

		return $this->curl($request);
	}
	
	/**
	 * @access private
	 * @return Response
	 */
	private function authorize(): ?Response{
		if ($this->accessToken && $this->accessToken->getRefreshToken()) {
			return $this->refreshAccessToken();
		}
		
		return $this->requestAccessToken();
	}
	
	/**
	 * @see https://tools.ietf.org/html/rfc6749#section-4.1.3
	 * @access private
	 * @return Response
	 */
	private function requestAccessToken(): ?Response{
		$request = new Request($this->authorizationURL, Request::METHOD_POST);
		
		$request->setParameter("grant_type", ClientCredentialsGrant::GRANT_TYPE_CLIENT_CREDENTIALS)
			->setParameter("client_id", $this->client_id)
			->setParameter("client_secret", $this->client_secret)
			->setHeader("Content-Type", "application/x-www-form-urlencoded");
		
		$response = $this->send($request);
		
		$this->validateResponseToken($response);
			
		return $response;
	}
	
	/**
	 * @access private
	 * @return Response
	 */
	private function refreshAccessToken(): ?Response{
		$request = new Request($this->authorizationURL, Request::METHOD_POST);
		
		$request->setParameter("grant_type", ClientCredentialsGrant::GRANT_TYPE_REFRESH_TOKEN)
			->setParameter("refresh_token", $this->accessToken->getRefreshToken())
			->setHeader("Content-Type", "application/x-www-form-urlencoded");
		
		$response = $this->send($request);
		
		$this->validateResponseToken($response);
			
		return $response;
	}
	
	/**
	 * @access private
	 * @param Response $response
	 * @return bool
	 */
	private function validateResponseToken(Response $response = NULL): bool{
		$this->accessToken 	= NULL;
		
		if (!$response) {
			return false;
		}
		
		$token = json_decode($response->getContent());
		
		if (!$token
			|| !isset($token->access_token)
			|| !isset($token->expires_in)) {
			return false;
		}
		
		$this->accessToken = new BearerToken($token->access_token);
		$this->accessToken->setExpiration($token->expires_in);
		
		if (isset($token->refresh_token)) {
			$this->accessToken->setRefreshToken($token->refresh_token);
		}
		
		if (isset($token->scope)) {
			foreach (implode(",", $token->scope) as $scope) {
				$this->accessToken->addScope($scope);
			}
		}
		
		return true;
	}
}
