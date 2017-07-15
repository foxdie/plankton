<?php

namespace Rest\Server;


final class Route{
	/**
	 * @access private
	 * @var string
	 */
	private $uri;
	
	/**
	 * @access private
	 * @var string
	 */
	private $method;
	
	/**
	 * @access public
	 * @param string $uri
	 * @param string $method
	 * @return void
	 */
	public function __construct($uri, $method = Request::METHOD_GET){
		$this->uri = $uri;
		$this->method = $method;
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
	 * @return string
	 */
	public function getMethod(){
		return $this->method;
	}

	/**
	 * @access public
	 * @param string $uri
	 * @return \Rest\Server\Route
	 */
	public function setURI($uri){
		$this->uri = $uri;
		
		return $this;
	}
	
	/**
	 * @access public
	 * @param string $method
	 * @return \Rest\Server\Route
	 */
	public function setMethod($method){
		$this->method = $method;
		
		return $this;
	}
	
	/**
	 * @access public
	 * @param Request $request
	 * @return bool
	 */
	public function matchRequest(Request $request){
		if ($request->getMethod() != $this->getMethod()) {
			return false;
		}
		
		return preg_match($this->getRegexp(), $request->getURI());
	}
	
	/**
	 * @access private
	 * @return string
	 */
	private function getRegExp(){
		$uri = preg_replace("/{[^}]+}/", "[^\/]+", $this->uri);
		
		return "#^" . $uri . "\$#";
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function __toString(){
		return "+ {$this->method} {$this->uri}";
	}
}
