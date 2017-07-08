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
	 * @param string $name
	 * @return string|false
	 */
	public function getHeader($name){
		foreach ($this->headers as $key => $value) {
			if (strtolower($name) == strtolower($key)) {
				return $value;
			}
		}
		
		return false;
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
