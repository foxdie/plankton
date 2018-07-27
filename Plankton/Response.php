<?php

namespace Plankton;


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

		$this->setHeader("Cache-Control", 	["no-cache", "no-store", "must-revalidate"]);
		$this->setHeader("Content-Length", 	strlen($content));
		$this->setHeader("Content-Type", 	self::CONTENT_TYPE_JSON);
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function getContent(): string{
		return $this->content;
	}
	
	/**
	 * @access public
	 * @param mixed $content
	 * @return \Plankton\Response
	 */
	public function setContent($content): Response{
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
		
		$this->content = $content;
		$this->setHeader("Content-Length", strlen($this->content));
		
		return $this;
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function getContentType(): string{
		return $this->getHeader("Content-Type");
	}
	
	/**
	 * @access public
	 * @param string $contentType
	 * @return \Plankton\Response
	 */
	public function setContentType(string $contentType): Response{
		return $this->setHeader("Content-Type", $contentType);
	}
	
	/**
	 * @access public
	 * @return int
	 */
	public function getCode(): int{
		return $this->code;
	}
	
	/**
	 * @access public
	 * @param int $code
	 * @return \Plankton\Response
	 */
	public function setCode(int $code): Response{
		$this->code = intval($code) ?: 200;
		
		return $this;
	}
	
	/**
	 * @access public
	 * @return int|null
	 */
	public function getContentLength(): ?int{
		return $this->getHeader("Content-Length");
	}
	
	/**
	 * @access public
	 * @return string|null
	 */
	public function getLocation(): ?string{
		return $this->getHeader("Location");
	}
	
	/**
	 * @access public
	 * @param string $location
	 * @return \Plankton\Response
	 */
	public function setLocation(string $location): Response{
		return $this->setHeader("Location", $location);
	}
	
	/**
	 * @access public
	 * @return string[]
	 */
	public function getHeaders(): array{
		return $this->headers;
	}
	
	/**
	 * @access public
	 * @param string $name
	 * @return string|null
	 */
	public function getHeader(string $name): ?string{
		foreach ($this->headers as $key => $value) {
			if (strtolower($name) == strtolower($key)) {
				return $value;
			}
		}
		
		return null;
	}
	
	/**
	 * @access public
	 * @param string $key
	 * @param mixed $value
	 * @return \Plankton\Response
	 */
	public function setHeader(string $key, $value): Response{
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
	public function __toString(): string{
		return $this->content . "\n\n";
	}
}
