<?php

namespace OAuth2;

use Rest\Server\Server;


class AuthorizationServer extends Server{
	public function __construct(){
		parent::__construct();
		
		$this->addMiddleware(new ClientCredentialsMiddleware())
			->registerController(new AccessTokenController());
	}
}
