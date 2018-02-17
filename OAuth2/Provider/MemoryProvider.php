<?php

namespace OAuth2\Provider;


class MemoryProvider implements AccessTokenProvider{
	/**
	 * @access private
	 * @var array[]
	 */
	private $clients;
	
	/**
	 * @access public
	 */
	public function __construct(){
		$this->clients = [];
	}
	
	public function addClient($client_id, $client_secret): void{
		$this->clients[$client_id] = [
			"client_secret" => $client_secret,
			"access_token" 	=> $this->createAccessToken()
		];
	}
	
	public function getAccessToken(string $client_id, string $client_secret): ?AccessToken{
		if (!isset($this->clients[$client_id])
			|| $this->clients[$client_id]["client_secret"] !== $client_secret) {
			return null;
		}
			
		$this->clients[$client_id]["access_token"] = $this->createToken();
		
		return $this->clients[$client_id]["access_token"];
	}
	
	public function refreshToken(string $accessToken): AccessToken{
		foreach ($this->clients as $client_id => $client) {
			if ($client["access_token"] === $accessToken) {
				return $this->getAccessToken($client_id, $client["client_secret"]);
			}
		}
		
		return null;
	}
	
	/**
	 * @access private
	 * @return AccessToken
	 */
	private function createToken(): AccessToken{
		return new BearerToken(sha1(uniqid(mt_rand(), true)));
	}
}
