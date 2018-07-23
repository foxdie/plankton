<?php

namespace Rest;


class BadRequestException extends \Rest\Exception{
	/**
	 * @access public
	 * @return void
	 */
	public function __construct(string $message = "Bad request"){
		parent::__construct($message, 400, NULL);
	}
}
