<?php

namespace Rest\OAuth2\Provider;

use Rest\OAuth2\Token\AccessToken;


interface AccessTokenProvider{
	/**
	 * @access public
	 * @param string $client_id
	 * @param string $client_secret
	 * @return AccessToken
	 */
	public function getAccessToken(string $client_id, string $client_secret): ?AccessToken;
	
	/**
	 * @access public
	 * @param string $refreshToken
	 * @return AccessToken
	 */
	public function refreshToken(string $refreshToken): ?AccessToken;
	
	/**
	 * @access public
	 * @param string $token
	 * @return bool
	 */
	public function isValidAccessToken(string $token): bool;
}
