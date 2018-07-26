<?php

namespace Rest\Logging;

use \SplObjectStorage;
use \SimpleXMLElement;
use Rest\Request;
use Rest\Response;


class SimpleLogger implements Logger{
	/**
	 * @access private
	 * @var SplObjectStorage
	 */
	private $logs;

	/**
	 * @access public
	 */
	public function __construct(){
		$this->logs = new SplObjectStorage();
	}

	/**
	 * {@inheritDoc}
	 * @see \Rest\Logging\Logger::log()
	 */

	public function log(Request $request, Response $response = NULL): void{
		$this->logs[$request] = $response;
	}

	/**
	 * @access public
	 * @return SimpleXMLElement
	 */
	public function getLogs(): SplObjectStorage{
		return $this->logs;
	}
}
