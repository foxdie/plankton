<?php

namespace Rest;


class UnauthorizedException extends \Rest\Exception{
	/**
	 * @access public
	 * @return void
	 */
	public function __construct(string $message = "Unauthorized"){
		parent::__construct($message, 401, NULL);
	}
}
