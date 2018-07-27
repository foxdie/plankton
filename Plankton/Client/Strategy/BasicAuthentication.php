<?php

namespace Plankton\Client\Strategy;

use Plankton\Request;
use Plankton\Response;


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
	 * @see \Plankton\Client\Strategy\AuthenticationStrategy::send()
	 */
	public function send(Request $request, callable $requestCallback): ?Response{
		$request->setHeader(
			"Authorization", 
			"Basic " . base64_encode("{$this->user}:{$this->password}")
		);

		return $requestCallback($request);
	}
}
