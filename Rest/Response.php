<?php

namespace Rest;


class Response{
	const CONTENT_TYPE_JSON = "application/json";
	const CONTENT_TYPE_XML = "application/xml";
	
	/**
	 * @access protected
	 * @var array
	 */
	protected $headers;
	
	/**
	 * @access protected
	 * @var string
	 */
	protected $contentType;
	
	/**
	 * @access protected
	 * @var int
	 */
	protected $code;
	
	/**
	 * @access protected
	 * @var string
	 */
	protected $location;
	
	/**
	 * @access protected
	 * @var string
	 */
	protected $data;
	
	/**
	 * @access public
	 * @static
	 * @param array $data
	 * @return string
	 */
	public static function toJSON(array $data){
		return json_encode($data);
	}
	
	/**
	 * @access public
	 * @static
	 * @param array $data
	 * @param string $rootName
	 * @return string
	 */
	public static function toXML(array $data, $rootName = "response"){
		$xml = new \SimpleXMLElement($rootName);
		
		$toXML = function(array $data, \SimpleXMLElement $xml) use (&$toXML){
			foreach ($data as $key => $value) {
				if (is_array($value)) {
					$child = $xml->addchild($key);
					$toXML($value, $child);
				}
				else {
					$xml->addChild($key, $value);
				}
			}
		};
		
		$toXML($data, $xml);

		return $xml->asXML();
	}
	
	/**
	 * @access public
	 * @param string $data
	 */
	public function __construct($data = ""){
		$this->data = $data;
		$this->headers = [];
		$this->contentType = self::CONTENT_TYPE_JSON;
		$this->location = NULL;
		$this->code = 200;
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function getData(){
		return $this->data;
	}
	
	/**
	 * @access public
	 * @param string $data
	 * @return \Rest\Response
	 */
	public function setData($data){
		if (is_array($data)) {
			switch ($this->contentType) {
				case self::CONTENT_TYPE_JSON :
					$data = self::toJSON($data);
					break;
				case self::CONTENT_TYPE_XML :
					$data = self::toXML($data);
					break;
			}
		}
		
		$this->data = $data;
		
		return $this;
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function getContentType(){
		return $this->contentType;
	}
	
	/**
	 * @access public
	 * @param string $contentType
	 * @return \Rest\Response
	 */
	public function setContentType($contentType){
		$this->contentType = $contentType;
		
		return $this;
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
	public function getContentLenght(){
		return strlen($this->data);
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function getLocation(){
		return isset($this->headers["Location"]) ? $this->headers["Location"] : NULL;
	}
	
	/**
	 * @access public
	 * @param string $location
	 * @return \Rest\Response
	 */
	public function setLocation($location){
		$this->headers["Location"] = $location;
		
		return $this;
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
	 * @param string $key
	 * @param string $value
	 * @return \Rest\Response
	 */
	public function setHeader($key, $value){
		$this->headers[$key] = $value;
		
		return $this;
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function __toString(){
		return $this->data;
	}
}
