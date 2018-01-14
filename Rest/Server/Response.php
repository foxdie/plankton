<?php

namespace Rest\Server;


class Response extends \Rest\Response{
	/**
	 * @access public
	 * @static
	 * @param array $data
	 * @return string
	 */
	public static function serializeJSON(array $data): string{
		return json_encode($data);
	}
	
	/**
	 * @access public
	 * @static
	 * @param array $data
	 * @param string $rootName
	 * @return string|null
	 */
	public static function serializeXML(array $data, string $rootName = "response"): ?string{
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
	
		return $xml->asXML() ?? null;
	}
	
	/**
	 * @access public
	 * @param string $content
	 */
	public function __construct(string $content = ""){
		$this->content = $content;
		$this->code = 200;
	
		$this->setHeader("Content-Length", 	strlen($content));
		$this->setHeader("Content-Type", 	self::CONTENT_TYPE_JSON);
		$this->setHeader("Cache-Control", 	["no-cache", "no-store", "must-revalidate"]);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Rest\Response::setContent()
	 */
	public function setContent($content): \Rest\Response{
		if (is_array($content)) {
			switch ($this->headers["Content-Type"]) {
				case self::CONTENT_TYPE_JSON :
					$content = self::serializeJSON($content);
					break;
				case self::CONTENT_TYPE_XML :
					$content = self::serializeXML($content);
					break;
			}
		}
		
		return parent::setContent($content);
	}
}
