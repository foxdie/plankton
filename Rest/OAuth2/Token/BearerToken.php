<?php

namespace Rest\OAuth2\Token;


class BearerToken implements AccessToken{
	/**
	 * @access private
	 * @var string
	 */
	private $accessToken;
	
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
	 * @param string $accessToken
	 */
	public function __construct(string $accessToken = ""){
		$this->accessToken 	= $accessToken;
		$this->expiration 	= time() + 3600 * 24;
		$this->scopes 		= [];
		$this->refreshToken = "";
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function getValue(): string{
		return $this->accessToken;
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function getAccessToken(): string{
		return $this->accessToken;
	}
	
	/**
	 * @access public
	 * @param string $token
	 * @return BearerToken
	 */
	public function setAccessToken(string $token): BearerToken{
		$this->accessToken = $token;
	
		return $this;
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
	 * @param int $expiration
	 * @return BearerToken
	 */
	public function setExpiration(int $expiration): BearerToken{
		$this->expiration = $expiration;
		
		return $this;
	}
	
	/**
	 * @access public
	 * @return bool
	 */
	public function isExpired(): bool{
		return $this->expiration < time();
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
	 * @param string $token
	 * @return BearerToken
	 */
	public function setRefreshToken(string $token): BearerToken{
		$this->refreshToken = $token;
	
		return $this;
	}
	
	/**
	 * @access public
	 * @return string[]
	 */
	public function getScopes(): array{
		return $this->scopes;
	}
	
	/**
	 * @access public
	 * @param string $scope
	 * @return BearerToken
	 */
	public function addScope(string $scope): BearerToken{
		$this->scopes[] = $scope;
	
		return $this;
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function __toString(): string{
		return $this->accessToken;
	}
}
