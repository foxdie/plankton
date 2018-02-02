<?php

namespace Rest\Client\Auth;

use Rest\Request;
use Rest\Client\Response;


abstract class AuthenticationStrategy{
	/**
	 * @access protected
	 * @var bool $enableSSL
	 */
	protected $enableSSL;
	
	/**
	 * @abstract
	 * @param Request $request
	 * @return Response
	 */
	abstract public function send(Request $request): ?Response;
	
	/**
	 * @access public
	 * @param bool $enableSSL
	 * @return void
	 */
	public function enableSSL(bool $enableSSL = true): void{
		$this->enableSSL = !!$enableSSL;
	}
	
	/**
	 * @access protected
	 * @param Request $request
	 * @return \Rest\Client\Response
	 */
	protected function curl(Request $request): ?Response{
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
}
