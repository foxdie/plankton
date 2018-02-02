<?php

namespace Rest\Client\Auth;

use Rest\Request;
use Rest\Client\Response;


class BasicAuthentication extends AuthenticationStrategy{
	/**
	 * @access private
	 * @var string
	 */
	private $user;
	
	/**
	 * @access private
	 * @var string
	 */
	private $password;
	
	/**
	 * @param string $user
	 * @param string $password
	 */
	public function __construct(string $user, string $password){
		$this->user = $user;
		$this->password = $password;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Rest\Client\Auth\AuthenticationStrategy::send()
	 */
	public function send(Request $request): ?Response{
		$request->setHeader(
			"Authorization", 
			"Basic " . base64_encode("{$this->user}:{$this->password}")
		);

		return $this->curl($request);
	}
}
