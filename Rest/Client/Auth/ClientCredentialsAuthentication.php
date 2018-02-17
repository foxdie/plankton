<?php

namespace Rest\Client\Auth;

use Rest\Request;
use Rest\Response;
use OAuth2\Grant\ClientCredentialsGrant;
use OAuth2\Token\BearerToken;


class ClientCredentialsAuthentication implements AuthenticationStrategy{
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
	public function send(Request $request, callable $requestCallback): ?Response{
		if (!$request->hasParameter("grant_type")) { // do not loop
			if (!$this->isAuthorized()) {
				$response = $this->authorize($requestCallback);
				
				if (!$this->isAuthorized()) {
					return $response;
				}
			}
			
			$request->setHeader(
				"Authorization",
				"Bearer " . base64_encode($this->accessToken->getValue())
			);
		}

		return $requestCallback($request);
	}
	
	/**
	 * @access private
	 * @return bool
	 */
	private function isAuthorized(): bool{
		return $this->accessToken && !$this->accessToken->isExpired();
	}
	
	/**
	 * @access private
	 * @param callable $requestCallback
	 * @return Response
	 */
	private function authorize(callable $requestCallback): ?Response{
		if ($this->accessToken && $this->accessToken->getRefreshToken()) {
			return $this->refreshAccessToken($requestCallback);
		}
		
		return $this->requestAccessToken($requestCallback);
	}
		
	/**
	 * @see https://tools.ietf.org/html/rfc6749#section-4.1.3
	 * @access private
	 * @param callable $requestCallback
	 * @return Response
	 */
	private function requestAccessToken(callable $requestCallback): ?Response{
		$request = new Request($this->authorizationURL, Request::METHOD_POST);
		
		$request->setData([
			"grant_type" 	=> ClientCredentialsGrant::GRANT_TYPE_CLIENT_CREDENTIALS,
			"client_id"		=> $this->client_id,
			"client_secret" => $this->client_secret,
			"Content-Type"	=> "application/x-www-form-urlencoded"
		]);
		
		$response = $requestCallback($request);
		
		$this->validateResponseToken($response);
			
		return $response;
	}
	
	/**
	 * @access private
	 * @param callable $requestCallback
	 * @return Response
	 */
	private function refreshAccessToken(callable $requestCallback): ?Response{
		$request = new Request($this->authorizationURL, Request::METHOD_POST);
		
		$request->setData([
			"grant_type" 	=> ClientCredentialsGrant::GRANT_TYPE_REFRESH_TOKEN,
			"refresh_token" => $this->accessToken->getRefreshToken()
		]);
		
		$request->setHeader("Content-Type", 	"application/x-www-form-urlencoded");
		
		$response = $requestCallback($request);
		
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
			foreach (implode(" ", $token->scope) as $scope) {
				$this->accessToken->addScope($scope);
			}
		}
		
		return true;
	}
}
