<?php

namespace Rest\Client;


class Request extends \Rest\Request{
	/**
	 * @access protected
	 * @var array
	 */
	protected $data;
	
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
	
	/**
	 * @access public
	 * return array
	 */
	public function getData(){
		return $this->data;
	}
	
	/**
	 * @access public
	 * @param array $data
	 * @return \Rest\Client\Request
	 */
	public function setData(array $data){
		$this->data = $data;
		
		return $this;
	}
}
