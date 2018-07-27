<?php

namespace Plankton\Server;

use \SplDoublyLinkedList;
use Plankton\Request;
use Plankton\Response;


class RequestDispatcher{
	/**
	 * @access private
	 * @var \SplDoublyLinkedList
	 */
	private $handlers;
		
	/**
	 * @access public
	 */
	public function __construct(){
		$this->handlers = new SplDoublyLinkedList();
	}

	/**
	 * @access public
	 * @param Request $request
	 * @throws RuntimeException
	 * @return Response
	 */
	public function process(Request $request): Response{
		if (!$this->handlers->count()) {
			throw new \RuntimeException();
		}
		
		$handler = $this->handlers->current();
		$this->handlers->next();
		
		return $handler->process($request, $this);
	}
	
	/**
	 * @access public
	 * @param RequestHandler $handler
	 * @return RequestDispatcher
	 */
	public function pipe(RequestHandler $handler): RequestDispatcher{
		$this->handlers->push($handler);
		$this->handlers->rewind();
		
		return $this;
	}
}
