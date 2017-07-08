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
	 * @param string $content
	 */
	public function __construct($content = ""){
		$this->content = $content;
		$this->code = 200;
	
		$this->setHeader("Content-length", 	strlen($content));
		$this->setHeader("Content-type", 	self::CONTENT_TYPE_JSON);
		$this->setHeader("Cache-Control", 	["no-cache", "no-store", "must-revalidate"]);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Rest\Response::setContent()
	 */
	public function setContent($content){
		if (is_array($content)) {
			switch ($this->headers["Content-type"]) {
				case self::CONTENT_TYPE_JSON :
					$content = self::serializeJSON($content);
					break;
				case self::CONTENT_TYPE_XML :
					$content = self::serializeXML($content);
					break;
			}
		}
		
		parent::setContent($content);
	}
}
