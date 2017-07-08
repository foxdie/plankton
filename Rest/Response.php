<?php

namespace Rest;


class Response{
	const CONTENT_TYPE_JSON = "application/json";
	const CONTENT_TYPE_XML 	= "application/xml";
	
	/**
	 * @access protected
	 * @var array
	 */
	protected $headers;
	
	/**
	 * @access protected
	 * @var int
	 */
	protected $code;
	
	/**
	 * @access protected
	 * @var string
	 */
	protected $content;

	/**
	 * @access public
	 * @param string $content
	 */
	public function __construct($content = ""){
		$this->content = $content;
		$this->code = 200;
		
		$this->setHeader("Content-length", 	strlen($content));
		$this->setHeader("Content-type", 	self::CONTENT_TYPE_JSON);
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function getContent(){
		return $this->content;
	}
	
	/**
	 * @access public
	 * @param string $content
	 * @return \Rest\Response
	 */
	public function setContent($content){
		$this->content = $content;
		$this->setHeader("Content-length", strlen($this->content));
		
		return $this;
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function getContentType(){
		return $this->getHeader("Content-type");
	}
	
	/**
	 * @access public
	 * @param string $contentType
	 * @return \Rest\Response
	 */
	public function setContentType($contentType){
		return $this->setHeader("Content-type", $contentType);
	}
	
	/**
	 * @access public
	 * @return number
	 */
	public function getCode(){
		return $this->code;
	}
	
	/**
	 * @access public
	 * @param int $code
	 * @return \Rest\Response
	 */
	public function setCode($code){
		$this->code = intval($code) ?: 200;
		
		return $this;
	}
	
	/**
	 * @access public
	 * @return int
	 */
	public function getContentLength(){
		return $this->getHeader("Content-length");
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function getLocation(){
		return $this->getHeader("Location");
	}
	
	/**
	 * @access public
	 * @param string $location
	 * @return \Rest\Response
	 */
	public function setLocation($location){
		return $this->setHeader("Location", $location);
	}
	
	/**
	 * @access public
	 * @return string[]
	 */
	public function getHeaders(){
		return $this->headers;
	}
	
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
	 * @param mixed $value
	 * @return \Rest\Response
	 */
	public function setHeader($key, $value){
		if (is_array($value)) {
			$value = implode(", ", array_map("trim", $value));
		}
		
		$this->headers[trim($key)] = trim($value);
		
		return $this;
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function __toString(){
		return $this->content;
	}
}
