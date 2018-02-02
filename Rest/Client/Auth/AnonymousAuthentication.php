<?php

namespace Rest\Client\Auth;

use Rest\Request;
use Rest\Client\Response;


class AnonymousAuthentication extends AuthenticationStrategy{
	/**
	 * @access public
	 */
	public function __construct(){
		$this->enableSSL = true;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Rest\Client\Auth\AuthenticationStrategy::send()
	 */
	public function send(Request $request): ?Response{
		$response = $this->curl($request);
		
		// @todo
		
		return $response;
	}
}
