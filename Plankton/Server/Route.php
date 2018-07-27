<?php

namespace Plankton\Server;

use Plankton\Request;


final class Route{
	/**
	 * @access private
	 * @var string
	 */
	private $uri;
	
	/**
	 * @access private
	 * @var string
	 */
	private $method;
	
	/**
	 * @access public
	 * @param string $uri
	 * @param string $method
	 * @return void
	 */
	public function __construct(string $uri, string $method = Request::METHOD_GET){
		$this->uri = $uri;
		$this->method = $method;
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
	public function getMethod(): string{
		return $this->method;
	}

	/**
	 * @access public
	 * @param string $uri
	 * @return \Plankton\Server\Route
	 */
	public function setURI(string $uri): Route{
		$this->uri = $uri;
		
		return $this;
	}
	
	/**
	 * @access public
	 * @param string $method
	 * @return \Plankton\Server\Route
	 */
	public function setMethod(string $method): Route{
		$this->method = $method;
		
		return $this;
	}
	
	/**
	 * @access public
	 * @param Request $request
	 * @return bool
	 */
	public function matchRequest(Request $request): bool{
		if ($request->getMethod() != $this->getMethod()) {
			return false;
		}
		
		return preg_match($this->getRegexp(), $request->getURI());
	}
	
	/**
	 * @access private
	 * @return string
	 */
	private function getRegExp(): string{
		$uri = preg_replace("/{[^}]+}/", "[^\/]+", $this->uri);
		
		return "#^" . $uri . "\$#";
	}
	
	/**
	 * @access public
	 * @return string
	 */
	public function __toString(): string{
		return "+ {$this->method} {$this->uri}";
	}
}

