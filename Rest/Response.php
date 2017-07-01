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
	protected $data;
	
	/**
	 * @access public
	 * @static
	 * @param array $data
	 * @return string
	 */
	public static function serializeJSON(array $data){
		return json_encode($data);
	}
	
	/**
	 * @access public
	 * @static
	 * @param array $data
	 * @param string $rootName
	 * @return string
	 */
	public static function serializeXML(array $data, $rootName = "response"){
		$xml = new \SimpleXMLElement("<{$rootName}/>");
		
		$serializeXML = function(array $data, \SimpleXMLElement $xml) use (&$serializeXML){
			foreach ($data as $key => $value) {
				if (is_array($value)) {
					$child = $xml->addchild($key);
					$serializeXML($value, $child);
				}
				else {
					$xml->addChild($key, $value);
				}
			}
		};
		
		$serializeXML($data, $xml);

		return $xml->asXML();
	}
	
	/**
	 * @access public
	 * @param string $data
	 */
	public function __construct($data = ""){
		$this->data = $data;
		$this->code = 200;
		
		$this->setHeader("Content-length", 	strlen($data));
		$this->setHeader("Content-type", 	self::CONTENT_TYPE_JSON);
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
			switch ($this->headers["Content-type"]) {
				case self::CONTENT_TYPE_JSON :
					$this->data = self::serializeJSON($data);
					break;
				case self::CONTENT_TYPE_XML :
					$this->data = self::serializeXML($data);
					break;
				default :
					$this->data = $data;
			}
		}
		
		$this->setHeader("Content-length", strlen($this->data));
		
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
	public function getContentLenght(){
		return $this->getHeader("Content-lenght");
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
