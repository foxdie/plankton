<?php

namespace Test\Middleware;


use Rest\Request;
use Rest\Server\Response;
use Rest\Server\Middleware;
use Rest\Server\RequestDispatcher;

class LogMiddleware implements Middleware{
	/**
	 * {@inheritDoc}
	 * @see \Rest\RequestHandler::process()
	 */
	public function process(Request $request, RequestDispatcher $dispatcher): Response{
		$this->log($request);
		
		return $dispatcher->process($request);
	}
	
	/**
	 * @access private
	 * @return void
	 */
	private function log(Request $request): void{
		$fp = fopen("php://temp", "w");
		fwrite($fp, date("Y-m-d H:i:s"). ": " . $request->getURI() . PHP_EOL);
		fclose($fp);
	}
}
