<?php

namespace Test\Middleware;


use Plankton\Request;
use Plankton\Response;
use Plankton\Server\Middleware;
use Plankton\Server\RequestDispatcher;

class LogMiddleware implements Middleware{
	/**
	 * {@inheritDoc}
	 * @see \Plankton\RequestHandler::process()
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
