<?php

namespace OAuth2\Token;


class BearerToken implements AccessToken{
	/**
	 * @access private
	 * @var string
	 */
	private $value;
	
	/**
	 * @access private
	 * @var int
	 */
	private $expiration;
	
	/**
	 * @access private
	 * @var string
	 */
	private $refreshToken;
	
	/**
	 * @access private
	 * @var string[]
	 */
	private $scopes;
	
	/**
	 * @access public
	 * @param string $value
	 */
	public function __construct(string $value){
		$this->value 		= $value;
		$this->expiration 	= 3600 * 24;
		$this->scopes 		= [];
		$this->refreshToken = "";
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function getValue(): string{
		return $this->value;
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function getType(): string{
		return AccessToken::TYPE_BEARER;
	}
	
	/**
	 * @access public
	 * @return int
	 */
	public function getExpiration(): int{
		return $this->expiration;
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function getRefreshToken(): ?string{
		return $this->refreshToken;
	}
	
	/**
	 * @access public
	 * @return string[]
	 */
	public function getScopes(): array{
		return $this->scopes;
	}
}
