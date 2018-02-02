<?php

namespace Rest;


class Request{
	const METHOD_GET 	= "GET";
	const METHOD_POST 	= "POST";
	const METHOD_PUT 	= "PUT";
	const METHOD_PATCH 	= "PATCH";
	const METHOD_DELETE = "DELETE";

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
	protected $scheme;
	
	/**
	 * @access protected
	 * @var string
	 */
	protected $host;
	
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
	 * @param string $url
	 * @param string $method
	 */
	public function __construct(string $url, string $method = self::METHOD_GET){
		$this->headers = [];
		$this->method = $method;
		$this->data = NULL;
		
		$url = parse_url($url);
		
		$this->uri = $this->sanitizeURI($url["path"]);
		$this->scheme = $url["scheme"];
		$this->host = $url["host"];
		$this->parameters = isset($url["query"]) ? parse_str($url["query"]) : [];
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
	 * @return mixed
	 */
	public function getParameter(string $name){
		return $this->parameters[$name] ?? null;
	}
	
	/**
	 * @access public
	 * @param string $name
	 * @param mixed $value
	 * @return \Rest\Request
	 */
	public function setParameter(string $name, $value): Request{
		$this->parameters[$name] = $value;
		
		return $this;
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function getURL(): string{
		return "{$this->scheme}://{$this->host}{$this->uri}";
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
	 * @return string
	 */
	public function getHost(): string{
		return $this->host;
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function getScheme(): string{
		return $this->scheme;
	}

	/**
	 * @access public
	 * @param string $key optional
	 * return mixed
	 */
	public function getData(string $key = null){
		if ($key === null) {
			return $this->data;
		}
		
		return $this->data[$key] ?? NULL;
	}
	
	/**
	 * @access public
	 * @param mixed $data
	 * @return \Rest\Request
	 */
	public function setData($data): Request{
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
	 * @param string $key
	 * @param string $value
	 * @return \Rest\Request
	 */
	public function setHeader(string $key, string $value): Request{
		$this->headers[$key] = $value;
	
		return $this;
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
	 * @param string $name
	 * @return bool
	 */
	public function hasHeader($name): bool{
		return $this->getHeader($name) !== null;	
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
