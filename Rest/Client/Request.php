<?php

namespace Rest\Client;


class Request extends \Rest\Request{
	/**
	 * @access public
	 * @param string $name
	 * @return string|NULL
	 */
	public function getHeader($name){
		return isset($this->headers[$name]) ? $this->headers[$name] : NULL;
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
