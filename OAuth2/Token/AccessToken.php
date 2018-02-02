<?php

namespace OAuth2\Token;


interface AccessToken{
	const TYPE_BEARER 	= "bearer";
	const TYPE_JWT 		= "json_web_token";
	
	public function getValue(): string;
	
	public function getType(): string;
	
	public function getExpiration(): int;
	
	public function getRefreshToken(): ?string;
	
	public function getScopes(): array;
}
