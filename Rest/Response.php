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
	public function __construct(string $content = ""){
		$this->content = $content;
		$this->code = 200;
		
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
	 * @param string $content
	 * @return \Rest\Response
	 */
	public function setContent(string $content): Response{
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
	 * @return \Rest\Response
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
	 * @return \Rest\Response
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
	 * @return \Rest\Response
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
	 * @return \Rest\Response
	 */
	public function setHeader(string $key, mixed $value): Response{
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
