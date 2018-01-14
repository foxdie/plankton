<?php

namespace Rest;


class Request{
	const METHOD_GET 		= "GET";
	const METHOD_POST 		= "POST";
	const METHOD_PUT 		= "PUT";
	const METHOD_PATCH 		= "PATCH";
	const METHOD_DELETE 	= "DELETE";

	/**
	 * @access protected
	 * @var array
	 */
	protected $headers;
	
	/**
	 * @access protected
	 * @var string
	 */
	protected $method;
	
	/**
	 * @acces protected
	 * @var string[]
	 */
	protected $parameters;
	
	/**
	 * @access protected
	 * @var string
	 */
	protected $uri;
	
	/**
	 * @access protected
	 * @var array
	 */
	protected $data;
	
	/**
	 * @access public
	 * @param string $uri
	 * @param string $method
	 */
	public function __construct(string $uri, string $method = self::METHOD_GET){
		$this->headers = [];
		$this->parameters = [];
		$this->uri = $this->sanitizeURI($uri);
		$this->method = $method;
		$this->data = [];
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function getMethod(): string{
		return $this->method;
	}
	
	/**
	 * @access public
	 * @return string[]
	 */
	public function getParameters(): array{
		return $this->parameters;
	}
	
	/**
	 * @access public
	 * @param string $name
	 * @return boolean|mixed
	 */
	public function getParameter(string $name){
		return isset($this->parameters[$name]) ? $this->parameters[$name] : false;
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function getURI(): string{
		return $this->uri;
	}

	/**
	 * @access public
	 * @param string $key optional
	 * return array|mixed|bool
	 */
	public function getData(string $key = false){
		if ($key === false) {
			return $this->data;
		}
		
		return isset($this->data[$key]) ? $this->data[$key] : null;
	}
	
	/**
	 * @access public
	 * @param array $data
	 * @return \Rest\Request
	 */
	public function setData(array $data): Request{
		$this->data = $data;
	
		return $this;
	}
	
	/**
	 * @access public
	 * @return array
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
	 * @access protected
	 * @param string $uri
	 * @return string
	 */
	protected function sanitizeURI(string $uri): string{
		$uri = preg_replace("/^(.*)#.+\$/U", "$1", $uri);
		$uri = preg_replace("/^(.*)\?.+\$/U", "$1", $uri);
		
		if (!$uri || $uri[0] != "/") {
			$uri = "/" . $uri;
		}
		
		if ($uri != "/" && $uri[strlen($uri) - 1] == "/") {
			$uri = substr($uri, 0, strlen($uri) - 1);
		}

		return $uri;
	}
}
