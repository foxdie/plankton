<?php

namespace Rest;


final class Request{
	const METHOD_GET 		= "GET";
	const METHOD_POST 		= "POST";
	const METHOD_PUT 		= "PUT";
	const METHOD_PATCH 		= "PATCH";
	const METHOD_DELETE 	= "DELETE";

	/**
	 * @access private
	 * @var string
	 */
	private $method;
	
	/**
	 * @acces private
	 * @var string[]
	 */
	private $parameters;
	
	/**
	 * @access private
	 * @var string
	 */
	private $uri;
	
	public function __construct(){
		$this->uri = $_SERVER["PHP_SELF"];
		$this->method = $_SERVER["REQUEST_METHOD"];
		$this->parameters = $this->parseQueryString();
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
	
	private function parseQueryString(){
		$query = $_SERVER["QUERY_STRING"];
		$parameters = [];
		
		foreach (explode("&", $query) as $parameter) {
			$parts = explode("=", $parameter);
			$name = $parts[0];
			$value = isset($parts[1]) ? $parts[1] : NULL;
				
			$this->parameters[$name] = $value;			
		}
	}
}
