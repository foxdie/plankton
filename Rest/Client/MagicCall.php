<?php

namespace Rest\Client;


final class MagicCall{
	/**
	 * @access private
	 * @var string[]
	 */
	private $calls;
	
	/**
	 * @access private
	 * @var array[]
	 */
	private $args;
	
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
	public function __construct(Client $client, string $name, array $args){
		$this->client 	= $client;
		$this->calls 	= [$name];
		$this->args 	= [$args];
	}
	
	/**
	 * @access public
	 * @magic
	 * @param string $name
	 * @param array $args
	 * @return \Rest\Client\MagicCall
	 */
	public function __call(string $name, array $args): MagicCall{
		$this->calls[] 	= $name;
		$this->args[] 	= $args;
	
		return $this;
	}
	
	/**
	 * @access public
	 */
	public function __destruct(){
		// @todo
	}
}
