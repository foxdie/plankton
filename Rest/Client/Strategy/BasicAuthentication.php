<?php

namespace Rest\Client\Strategy;

use Rest\Request;
use Rest\Response;


class BasicAuthentication implements AuthenticationStrategy{
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
	 * @see \Rest\Client\Strategy\AuthenticationStrategy::send()
	 */
	public function send(Request $request, callable $requestCallback): ?Response{
		$request->setHeader(
			"Authorization", 
			"Basic " . base64_encode("{$this->user}:{$this->password}")
		);

		return $requestCallback($request);
	}
}
