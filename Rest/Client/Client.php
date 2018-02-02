<?php

namespace Rest\Client;

use Rest\Response;
use Rest\Request;
use Rest\Client\Auth\AuthenticationStrategy;
use Rest\Client\Auth\AnonymousAuthentication;


class Client{
	/**
	 * @access protected
	 * @var string $apiEntryPoint
	 */
	protected $apiEntryPoint;

	/**
	 * @access protected
	 * @var AuthenticationStrategy
	 */
	protected $strategy;
	
	/**
	 * @access public
	 * @param string $apiEntryPoint
	 */
	public function __construct(string $apiEntryPoint, AuthenticationStrategy $strategy = NULL){
		$this->apiEntryPoint = $apiEntryPoint;
		$this->strategy = $strategy ?: new AnonymousAuthentication();
	}
	
	/**
	 * @access public
	 * @param string $uri
	 * @param callable $callback
	 * @return \Rest\Response|null
	 */
	public function get(string $uri, callable $callback = NULL): ?Response{
		$request = new Request($this->apiEntryPoint . $uri, Request::METHOD_GET);
		
		return $this->send($request, $callback);
	}
	
	/**
	 * @access public
	 * @param string $uri
	 * @param array $data
	 * @param callable $callback
	 * @return \Rest\Response|null
	 */
	public function post(string $uri, array $data, callable $callback = NULL): ?Response{
		$request = new Request($this->apiEntryPoint . $uri, Request::METHOD_POST);
		$request->setData($data);
		
		return $this->send($request, $callback);
	}

	/**
	 * @access public
	 * @param string $uri
	 * @param array $data
	 * @param callable $callback
	 * @return \Rest\Response|null
	 */
	public function put(string $uri, array $data, callable $callback = NULL): ?Response{
		$request = new Request($this->apiEntryPoint . $uri, Request::METHOD_PUT);
		$request->setData($data);
		
		return $this->send($request, $callback);
	}
	
	/**
	 * @access public
	 * @param string $uri
	 * @param array $data
	 * @param callable $callback
	 * @return \Rest\Response|null
	 */
	public function patch(string $uri, array $data, callable $callback = NULL): ?Response{
		$request = new Request($this->apiEntryPoint . $uri, Request::METHOD_PATCH);
		$request->setData($data);
		
		return $this->send($request, $callback);
	}
	
	/**
	 * @access public
	 * @param string $uri
	 * @param callable $callback
	 * @return \Rest\Response|null
	 */
	public function delete(string $uri, callable $callback = NULL): ?Response{
		$request = new Request($this->apiEntryPoint . $uri, Request::METHOD_DELETE);

		return $this->send($request, $callback);
	}
	
	/**
	 * @access protected
	 * @param Request $request
	 * @param callable $callback
	 * @throws \InvalidArgumentException
	 * @return \Rest\Response|null
	 */
	protected function send(Request $request, callable $callback = NULL): ?Response{
		$response = $this->strategy->send($request);
		// @todo response may be NULL
		
		if (!$callback) {
			return $response;
		}
		
		if (!is_callable($callback)) {
			throw new \InvalidArgumentException("Invalid callback");
		}
		
		return call_user_func($callback, $response);
	}
	
	/**
	 * @access public
	 * @param bool $enableSSL
	 * @return \Rest\Client
	 */
	public function enableSSL(bool $enableSSL = true): Client{
		$this->strategy->enableSSL(!!$enableSSL);
		
		return $this;
	}
}
