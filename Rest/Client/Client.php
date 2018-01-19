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
	public function __construct(string $apiEntryPoint){
		$this->apiEntryPoint = $apiEntryPoint;
		$this->enableSSL = true;
	}
	
	/**
	 * @access public
	 * @param string $uri
	 * @param callable $callback
	 * @return \Rest\Client\Response|null
	 */
	public function get(string $uri, callable $callback = NULL): ?Response{
		$request = new Request($uri, Request::METHOD_GET);
		
		return $this->send($request, $callback);
	}
	
	/**
	 * @access public
	 * @param string $uri
	 * @param array $data
	 * @param callable $callback
	 * @return \Rest\Client\Response|null
	 */
	public function post(string $uri, array $data, callable $callback = NULL): ?Response{
		$request = new Request($uri, Request::METHOD_POST);
		$request->setData($data);
		
		return $this->send($request, $callback);
	}

	/**
	 * @access public
	 * @param string $uri
	 * @param array $data
	 * @param callable $callback
	 * @return \Rest\Client\Response|null
	 */
	public function put(string $uri, array $data, callable $callback = NULL): ?Response{
		$request = new Request($uri, Request::METHOD_PUT);
		$request->setData($data);
		
		return $this->send($request, $callback);
	}
	
	/**
	 * @access public
	 * @param string $uri
	 * @param array $data
	 * @param callable $callback
	 * @return \Rest\Client\Response|null
	 */
	public function patch(string $uri, array $data, callable $callback = NULL): ?Response{
		$request = new Request($uri, Request::METHOD_PATCH);
		$request->setData($data);
		
		return $this->send($request, $callback);
	}
	
	/**
	 * @access public
	 * @param string $uri
	 * @param callable $callback
	 * @return \Rest\Client\Response|null
	 */
	public function delete(string $uri, callable $callback = NULL): ?Response{
		$request = new Request($uri, Request::METHOD_DELETE);

		return $this->send($request, $callback);
	}
	
	/**
	 * @access protected
	 * @param Request $request
	 * @param callable $callback
	 * @throws \InvalidArgumentException
	 * @return \Rest\Client\Response|null
	 */
	protected function send(Request $request, callable $callback = NULL): ?Response{
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
	 * @param bool $enableSSL
	 * @return \Rest\Client
	 */
	public function enableSSL(bool $enableSSL = true): Client{
		$this->enableSSL = $enableSSL ? true : false;
		
		return $this;
	}
	
	/**
	 * @access private
	 * @param Request $request
	 * @return \Rest\Client\Response|null
	 */
	private function curl(Request $request): ?Response{
		$ch = \curl_init($this->apiEntryPoint . $request->getURI());

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->enableSSL);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->enableSSL ? 2 : 0);
		
		// capture headers
		$headers = [];
		curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($ch, $str) use (&$headers){
			$headers[] = $str;
			return strlen($str);
		});

		// method
		switch ($request->getMethod()) {
			case Request::METHOD_DELETE:
			case Request::METHOD_PATCH:
			case Request::METHOD_PUT:
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request->getMethod());
				curl_setopt($ch, CURLOPT_POSTFIELDS, $this->buildQuery($request));
				break;
			case Request::METHOD_POST:
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $this->buildQuery($request));
		}
		
		// request
		$content = curl_exec($ch);
		
		if ($content === false || curl_errno($ch)) {
			return null;
		}

		$infos = curl_getinfo($ch);
		curl_close($ch);
		
		// response
		$response = new Response();
		$response
			->setContent($content)
			->setCode($infos["http_code"]);
		
		// set headers
		foreach ($headers as $name => $value) {
			if (preg_match("/^(.+): (.+)\$/", $value, $matches)) {
				$response->setHeader($matches[1], explode(",", $matches[2]));
			}
		}
			
		return $response;
	}
	
	/**
	 * @access private
	 * @param Request $request
	 * @return string
	 */
	private function buildQuery(Request $request): string{
		if (is_array($request->getData())) {
			return http_build_query($request->getData());
		}
		
		return $request->getData();
	}
}
