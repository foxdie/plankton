<?php

namespace OAuth2\Provider;

use OAuth2\Token\AccessToken;


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
	 * @param string $accessToken
	 * @return AccessToken
	 */
	public function refreshToken(string $accessToken): ?AccessToken;
}
