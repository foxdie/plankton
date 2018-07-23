<?php

namespace OAuth2\Token;


interface AccessToken{
	const TYPE_BEARER 	= "bearer";
	const TYPE_JWT 		= "json_web_token";
	
	/**
	 * @access public
	 * @return string
	 */
	public function getValue(): string;
	
	/**
	 * @access public
	 * @return string
	 */
	public function getType(): string;
	
	/**
	 * @access public
	 * @return int
	 */
	public function getExpiration(): int;
	
	/**
	 * @access public
	 * @return bool
	 */
	public function isExpired(): bool;
	
	/**
	 * @access public
	 * @return string
	 */
	public function getRefreshToken(): ?string;
	
	/**
	 * @access public
	 * @return string[]
	 */
	public function getScopes(): array;
}
