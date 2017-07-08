<?php

namespace Rest;


class Request{
	const METHOD_GET 		= "GET";
	const METHOD_POST 		= "POST";
	const METHOD_PUT 		= "PUT";
	const METHOD_PATCH 		= "PATCH";
	const METHOD_DELETE 	= "DELETE";

	/**
	 * @access protected
	 * @var array
	 */
	protected $headers;
	
	/**
	 * @access protected
	 * @var string
	 */
	protected $method;
	
	/**
	 * @acces protected
	 * @var string[]
	 */
	protected $parameters;
	
	/**
	 * @access protected
	 * @var string
	 */
	protected $uri;
	
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
		$this->headers = [];
		$this->parameters = [];
		$this->uri = $this->sanitizeURI($uri);
		$this->method = $method;
		$this->data = [];
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function getMethod(){
		return $this->method;
	}
	
	/**
	 * @access public
	 * @return \Rest\string[]
	 */
	public function getParameters(){
		return $this->parameters;
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function getURI(){
		return $this->uri;
	}

	/**
	 * @access public
	 * @param string $key optional
	 * return array|mixed
	 */
	public function getData($key = false){
		if ($key === false) {
			return $this->data;
		}
		
		return isset($this->data[$key]) ? $this->data[$key] : NULL;
	}
	
	/**
	 * @access public
	 * @param array $data
	 * @return \Rest\Request
	 */
	public function setData(array $data){
		$this->data = $data;
	
		return $this;
	}
	
	protected function sanitizeURI($uri){
		$uri = preg_replace("/^(.*)#.+\$/U", "$1", $uri);
		$uri = preg_replace("/^(.*)\?.+\$/U", "$1", $uri);
		
		if (!$uri || $uri[0] != "/") {
			$uri = "/" . $uri;
		}
		
		if ($uri != "/" && $uri[strlen($uri) - 1] == "/") {
			$uri = substr($uri, 0, strlen($uri) - 1);
		}

		return $uri;
	}
}
