<?php

namespace Rest;


class NotFoundException extends \Rest\Exception{
	/**
	 * @access public
	 * @return void
	 */
	public function __construct(){
		parent::__construct("Not Found", 404, NULL);
	}
}
