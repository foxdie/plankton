<?php

namespace Plankton\Logging;

use \SplObjectStorage;
use Plankton\Request;
use Plankton\Response;


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
	 * @see \Plankton\Logging\Logger::log()
	 */

	public function log(Request $request, Response $response = NULL): void{
		$this->logs[$request] = $response;
	}

	/**
	 * @access public
	 * @return SplObjectStorage
	 */
	public function getLogs(): SplObjectStorage{
		return $this->logs;
	}
}
