<?php

namespace Test\Middleware;


use Rest\Server\Request;
use Rest\Server\Response;
use Rest\Server\Middleware;
use Rest\Server\RequestDispatcher;

class BasicAuthenticationMiddleware implements Middleware{
	/**
	 * {@inheritDoc}
	 * @see \Rest\Server\RequestHandler::process()
	 */
	public function process(Request $request, RequestDispatcher $dispatcher): Response{
		if (!$this->isAuthenticated($request)) {
			$response = new Response();
			
			$response->setCode(401);
			$response->setContent("Unauthorized");
			
			return $response;
		}
		
		return $dispatcher->process($request);
	}
	
	/**
	 * @access private
	 * @return boolean
	 */
	private function isAuthenticated(Request $request): bool{
		if (!$request->getHeader("Authorization")) {
			return false;
		}
		
		$header = $request->getHeader("Authorization");
		
		if (!preg_match("/^Basic (.+)\$/U", $header, $matches)) {
			return false;
		}
		
		$authorization = base64_decode($matches[1]);
		
		if (!preg_match("/^([^:]+):(.+)\$/U", $authorization, $matches)) {
			return false;
		}
		
		list($user, $password) = explode(":", $authorization);
		
		return $this->isGranted($user, $password);
	}
	
	/**
	 * @access private
	 * @param string $user
	 * @param string $password
	 * @return bool
	 */
	private function isGranted(string $user, string $password): bool{
		return $user === "foo" && $password === "bar";
	}
}
