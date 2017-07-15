<?php

namespace Rest\Client;


class Request extends \Rest\Request{
	/**
	 * @access public
	 * @param string $uri
	 * @param string $method
	 */
	public function __construct($uri, $method = self::METHOD_GET){
		parent::__construct($uri, $method);
		
		$this->data = [];
	}

	/**
	 * @access public
	 * @param string $key
	 * @param string $value
	 * @return \Rest\Client\Request
	 */
	public function setHeader($key, $value){
		$this->headers[$key] = $value;
	
		return $this;
	}
}
