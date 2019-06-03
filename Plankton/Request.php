<?php

namespace Plankton;


class Request implements HTTPMessage{
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
	 * @var mixed
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
		$this->parameters = [];
		
		if (isset($url["query"])) {
            parse_str($url["query"], $this->parameters);
		}
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
	 * @param string $method
	 * @return \Plankton\Request
	 */
	public function setMethod(string $method): Request{
	   $this->method = $method;
	   
	   return $this;
	}
	
	/**
	 * @access public
	 * @return string|null
	 */
	public function getContentType(){
	    return $this->getHeader("Content-Type");
	}
	
	/**
	 * @access public
	 * @param string $contentType
	 * @return \Plankton\Request
	 */
	public function setContentType(string $contentType): Request{
	    return $this->setHeader("Content-Type", $contentType);
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
	 * @return \Plankton\Request
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
		$url = "{$this->scheme}://{$this->host}{$this->uri}";
		
		if (count($this->parameters)) {
		    $url .= "?" . http_build_query($this->parameters);
		}
		
		return $url;
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
	    if ($key) {
	        switch (true) {
	            case is_array($this->data):
	                return $this->data[$key] ?? NULL;
	            case is_object($this->data):
	                return $this->data->$key ?? NULL;
	            default:
	                return NULL;
	        }
	    }

	    return $this->data;
	}
	
	/**
	 * @access public
	 * @param mixed $data
	 * @return \Plankton\Request
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
	 * @return \Plankton\Request
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
	 * @access public
	 * @param string $name
	 * @return bool
	 */
	public function hasParameter($name): bool{
		return $this->getParameter($name) !== null;
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function __toString(): string{
	    switch ($this->getHeader("Content-Type")) {
	        case self::CONTENT_TYPE_JSON :
	            if (is_array($this->data) || is_object($this->data)) {
	                return json_encode($this->data);
	            }
	            
	            return $this->data;
	        case self::CONTENT_TYPE_X_WWW_FORM_URLENCODED:
	            if (is_array($this->data)) {
	                return http_build_query($this->data);
	            }
	            
	            if (is_object($this->data)) {
	                return http_build_query(json_decode(json_encode($this->data)));
	            }
	            
	            return $this->data;
	        default:
	            if (is_array($this->data)) {
	                return http_build_query($this->data);
	            }
	            
	            if (is_object($this->data)) {
	                return http_build_query(json_decode(json_encode($this->data)));
	            }
	            
	            return $this->data;
	    }
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
