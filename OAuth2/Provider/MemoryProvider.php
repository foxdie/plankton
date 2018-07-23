<?php

namespace OAuth2\Provider;

define("MEMORYPROVIDER_PATH", sys_get_temp_dir() . "/memory_provider_clients.txt");

use OAuth2\Token\AccessToken;
use OAuth2\Token\BearerToken;


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
		$this->restoreClients();
	}
	
	/**
	 * @access public
	 * @param int $client_id
	 * @param string $client_secret
	 * @return void
	 */
	public function addClient(int $client_id, string $client_secret): void{
		if (isset($this->clients[$client_id]) && $this->clients[$client_id]["client_secret"] == $client_secret) {
			return;
		}
		
		$this->clients[$client_id] = [
			"client_secret" => $client_secret,
			"access_token" 	=> $this->createToken()
		];
	}
	
	/**
	 * {@inheritDoc}
	 * @see \OAuth2\Provider\AccessTokenProvider::getAccessToken()
	 */
	public function getAccessToken(string $client_id, string $client_secret): ?AccessToken{
		if (!isset($this->clients[$client_id])
			|| $this->clients[$client_id]["client_secret"] !== $client_secret) {
			return null;
		}
		
		if (!($this->clients[$client_id]["access_token"] instanceof AccessToken)) {
			$this->clients[$client_id]["access_token"] = $this->createToken();
		}

		return $this->clients[$client_id]["access_token"];
	}
	
	
	/**
	 * {@inheritDoc}
	 * @see \OAuth2\Provider\AccessTokenProvider::refreshToken()
	 */
	public function refreshToken(string $refreshToken): AccessToken{
		foreach ($this->clients as $client_id => $client) {
			if ($client["access_token"]->getRefreshToken() === $refreshToken) {
				$this->clients[$client_id]["access_token"] = $this->createToken();
				
				return $this->clients[$client_id]["access_token"];
			}
		}
		
		return null;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \OAuth2\Provider\AccessTokenProvider::isValidAccessToken()
	 */
	public function isValidAccessToken(string $token): bool{
		foreach ($this->clients as $client) {
			if ($client["access_token"] instanceof AccessToken
				&& $client["access_token"]->getValue() == $token
				&& !$client["access_token"]->isExpired()) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * @access private
	 * @return void
	 */
	private function restoreClients(): void{
		if (!file_exists(MEMORYPROVIDER_PATH)) {
			return;
		}
		
		$this->clients = unserialize(file_get_contents(MEMORYPROVIDER_PATH));
	}
	
	/**
	 * @access public
	 */
	public function __destruct(){
		$fp = fopen(MEMORYPROVIDER_PATH, "w+");
		
		fputs($fp, serialize($this->clients));
		fclose($fp);
	}
	
	/**
	 * @access private
	 * @return AccessToken
	 */
	private function createToken(): AccessToken{
		$token = new BearerToken(sha1(uniqid(mt_rand(), true)));
		$token->setRefreshToken(sha1(uniqid(mt_rand(), true)));
		
		return $token;
	}
}
