<?php

namespace Rest\Server;


class Request extends \Rest\Request{
	/**
	 * @access public
	 */
	public function __construct(){
		parent::__construct(
			isset($_SERVER["PATH_INFO"]) ? $_SERVER["PATH_INFO"] : preg_replace("/^(.+)\?.*\$/", "\$1", $_SERVER["REQUEST_URI"]), 
			$_SERVER["REQUEST_METHOD"]
		);
		
		$this->parameters = $this->parseQueryString();
		$this->headers = $this->getHeaders();
	}
	
	/**
	 * @access private
	 * @return string[]
	 * @todo
	 */
	private function getHeaders(){
		return [];
	}
}
