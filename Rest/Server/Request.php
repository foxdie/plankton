<?php

namespace Rest\Server;


class Request extends \Rest\Request{
	/**
	 * @access public
	 */
	public function __construct(){
		parent::__construct(
			$_SERVER["PATH_INFO"] ?? preg_replace("/^(.+)\?.*\$/", "\$1", $_SERVER["REQUEST_URI"]), 
			$_SERVER["REQUEST_METHOD"]
		);

		parse_str(file_get_contents("php://input"), $data);
		$this->setData($data);
		
		parse_str($_SERVER["QUERY_STRING"], $this->parameters);
		$this->headers = getallheaders();
	}
}
