<?php

namespace Rest\Client;


use Rest\Request;
use Rest\Response;

final class MagicCall{
	/**
	 * @access private
	 * @var string
	 */
	private $uri;
	
	/**
	 * @access private
	 * @var Client
	 */
	private $client;
	
	/**
	 * @access public
	 * @param Client $client
	 * @param string $name
	 * @param array $args
	 */
	public function __construct(Client $client){
		$this->client 	= $client;
		$this->uri 		= "";
	}
	
	/**
	 * @access public
	 * @magic
	 * @param string $name
	 * @param array $args
	 * @return mixed MagicCall | Response
	 */
	public function __call(string $name, array $args){
		if (preg_match("#^(get|post|put|patch|delete)(.*)\$#", $name, $matches)) {
			if ($matches[2]) {
				$this->uri .= "/" . $this->spinalCase($matches[2]);
			}
			
			$method = $matches[1];
			
			switch (strtoupper($method)) {
				case Request::METHOD_POST:
				case Request::METHOD_PUT:
				case Request::METHOD_PATCH:
					return $this->client->$method($this->uri, $args[0]);
				case Request::METHOD_DELETE:
					if (count($args)) {
						$this->uri .= "/" . $this->spinalCase($args[0]);
					}
					
					return $this->client->$method($this->uri);
				default:
					return $this->client->$method($this->uri);
			}
		}
		
		$this->uri .= "/" . $this->spinalCase($name);
		
		if (count($args)) {
			$this->uri .= "/" . $this->spinalCase($args[0]);
		}

		return $this;
	}
	
	/**
	 * @access private
	 * @param string $string
	 * @return string
	 */
	private function spinalCase(string $string): string{
		$string[0] = strtolower($string[0]);
		
		$tokens = preg_split("#(?=[A-Z])#", $string);
		$tokens = array_map("strtolower", $tokens);
		
		return implode("-", $tokens);
	}
}
