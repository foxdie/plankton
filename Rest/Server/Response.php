<?php

namespace Rest\Server;


class Response extends \Rest\Response{
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
	 * {@inheritDoc}
	 * @see \Rest\Response::setData()
	 */
	public function setData($data){
		if (is_array($data)) {
			switch ($this->headers["Content-type"]) {
				case self::CONTENT_TYPE_JSON :
					$data = self::serializeJSON($data);
					break;
				case self::CONTENT_TYPE_XML :
					$data = self::serializeXML($data);
					break;
			}
		}
		
		parent::setData($data);
	}
}
