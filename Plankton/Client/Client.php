<?php

namespace Plankton\Client;

use Plankton\Response;
use Plankton\Request;
use Plankton\Client\Strategy\AuthenticationStrategy;
use Plankton\Client\Strategy\AnonymousAuthentication;
use Plankton\Logging\Logger;


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
	 * @access protected
	 * @var Logger
	 */
	protected $logger;
	
	/**
	 * @access protected
	 * @var bool $enableSSL
	 */
	protected $enableSSL;
	
	/**
	 * @access public
	 * @param string $apiEntryPoint
	 */
	public function __construct(string $apiEntryPoint, AuthenticationStrategy $strategy = NULL){
		$this->apiEntryPoint 	= $apiEntryPoint;
		$this->strategy 		= $strategy ?: new AnonymousAuthentication();
		$this->logger 			= NULL;
		$this->enableSSL 		= true;
	}

	/**
	 * @access public
	 * @param string $uri
	 * @param callable $callback
	 * @return \Plankton\Response|null
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
	 * @return \Plankton\Response|null
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
	 * @return \Plankton\Response|null
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
	 * @return \Plankton\Response|null
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
	 * @return \Plankton\Response|null
	 */
	public function delete(string $uri, callable $callback = NULL): ?Response{
		$request = new Request($this->apiEntryPoint . $uri, Request::METHOD_DELETE);

		return $this->send($request, $callback);
	}
	
	/**
	 * @access public
	 * @return Logger
	 */
	public function getLogger(): Logger{
		return $this->logger;
	}
	
	/**
	 * @access public
	 * @param Logger $logger
	 * @return Client
	 */
	public function setLogger(Logger $logger): Client{
		$this->logger = $logger;
		
		return $this;
	}
	
	/**
	 * @access public
	 * @param bool $enableSSL
	 * @return \Plankton\Client
	 */
	public function enableSSL(bool $enableSSL = true): Client{
		$this->enableSSL = !!$enableSSL;
	
		return $this;
	}
	
	/**
	 * @access protected
	 * @param Request $request
	 * @param callable $callback
	 * @throws \InvalidArgumentException
	 * @return \Plankton\Response|null
	 */
	protected function send(Request $request, callable $callback = NULL): ?Response{
		$response = $this->strategy->send($request, [$this, "curl"]);
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
	 * @param Request $request
	 * @return \Plankton\Response
	 */
	public function curl(Request $request): ?Response{
		$ch = \curl_init($request->getURL());
	
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->enableSSL);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->enableSSL ? 2 : 0);
	
		// send headers
		if ($request->getHeaders()) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $this->buildHeaders($request));
		}
	
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
			if ($this->logger) {
				$this->logger->log($request, null);
			}
			
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
		
		if ($this->logger) {
			$this->logger->log($request, $response);
		}
		
		return $response;
	}
	
	/**
	 * @access private
	 * @param Request $request
	 * @return string
	 */
	private function buildQuery(Request $request): ?string{
		if (is_array($request->getData())) {
			return http_build_query($request->getData());
		}
	
		return $request->getData();
	}
	
	/**
	 * @access private
	 * @param Request $request
	 * @return string[]
	 */
	private function buildHeaders(Request $request): array{
		$headers = [];
		foreach ($request->getHeaders() as $name => $value) {
			$headers[] = "{$name}: {$value}";
		}
	
		return $headers;
	}
	
	/**
	 * @access public
	 * @magic
	 * @param string $name
	 * @param array $args
	 * @return \Plankton\Client\MagicCall
	 */
	public function __call(string $name, array $args){
		$call = new MagicCall($this);
		
		return $call->__call($name, $args);
	}
}
