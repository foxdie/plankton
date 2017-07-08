<?php

namespace Rest\Client;

use Rest\Client\Response;
use Rest\Client\Request;


class Client{
	/**
	 * @access protected
	 * @var string
	 */
	protected $apiEntryPoint;

	private $enableSSL;
	
	/**
	 * @access public
	 * @param string $apiEntryPoint
	 */
	public function __construct($apiEntryPoint){
		$this->apiEntryPoint = $apiEntryPoint;
		$this->enableSSL = true;
	}
	
	/**
	 * @access public
	 * @param string $uri
	 * @param callable $callback
	 * @return \Rest\Client\Response|false
	 */
	public function get($uri, $callback = NULL){
		$request = new Request($uri, Request::METHOD_GET);
		
		return $this->send($request, $callback);
	}
	
	/**
	 * @access public
	 * @param string $uri
	 * @param string $data
	 * @param callable $callback
	 * @return \Rest\Client\Response|false
	 */
	public function post($uri, $data, $callback = NULL){
		$request = new Request($uri, Request::METHOD_POST);
		$request->setData($data);
		
		return $this->send($request, $callback);
	}

	/**
	 * @access public
	 * @param string $uri
	 * @param string $data
	 * @param callable $callback
	 * @return \Rest\Client\Response|false
	 */
	public function put($uri, $data, $callback = NULL){
		$request = new Request($uri, Request::METHOD_PUT);
		$request->setData($data);
		
		return $this->send($request, $callback);
	}
	
	/**
	 * @access public
	 * @param string $uri
	 * @param string $data
	 * @param callable $callback
	 * @return \Rest\Client\Response|false
	 */
	public function patch($uri, $data, $callback = NULL){
		$request = new Request($uri, Request::METHOD_PATCH);
		$request->setData($data);
		
		return $this->send($request, $callback);
	}
	
	/**
	 * @access public
	 * @param string $uri
	 * @param callable $callback
	 * @return \Rest\Client\Response|false
	 */
	public function delete($uri, $callback = NULL){
		$request = new Request($uri, Request::METHOD_DELETE);

		return $this->send($request, $callback);
	}
	
	/**
	 * @access protected
	 * @param Request $request
	 * @param callable $callback
	 * @throws \InvalidArgumentException
	 * @return \Rest\Client\Response|false
	 */
	protected function send(Request $request, $callback = NULL){
		$response = $this->curl($request);
		
		if (!$callback) {
			return $response;
		}
		
		if (!is_callable($callback)) {
			throw new \InvalidArgumentException("Invalid callback");
		}
		
		return is_array($callback) ? call_user_func_array($callback, $request) : call_user_func($callback, $response);
	}
	
	/**
	 * @access public
	 * @param string $enableSSL
	 * @return \Rest\Client
	 */
	public function enableSSL($enableSSL = true){
		$this->enableSSL = $enableSSL ? true : false;
		
		return $this;
	}
	
	/**
	 * @access private
	 * @param Request $request
	 * @return \Rest\Client\Response|false
	 */
	private function curl(Request $request){
		$ch = curl_init($this->apiEntryPoint . $request->getURI());

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->enableSSL);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->enableSSL ? 2 : 0);
		
		//capture headers
		$headers = [];
		curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($ch, $str) use (&$headers){
			$headers[] = $str;
			return strlen($str);
		});

		//post
		if ($request->getMethod() == Request::METHOD_POST) {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $request->getData());
		}
		
		//request
		$content = curl_exec($ch);
		
		if ($content === false || curl_errno($ch)) {
			return false;
		}

		$infos = curl_getinfo($ch);
		curl_close($ch);
		
		//response
		$response = new Response();
		$response
			->setContent($content)
			->setCode($infos["http_code"]);
		
		//set headers
		foreach ($headers as $name => $value) {
			if (preg_match("/^(.+): (.+)\$/", $value, $matches)) {
				$response->setHeader($matches[1], explode(",", $matches[2]));
			}
		}
			
		return $response;
	}
}
