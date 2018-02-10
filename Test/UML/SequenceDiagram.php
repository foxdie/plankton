<?php

namespace Test\UML;


class SequenceDiagram{
	/**
	 * @access public
	 * @param string $path
	 * @throws \RuntimeException
	 */
	public function __construct(string $path){
		if (!file_exists($path)) {
			throw new \RuntimeException("File not found: $path");
		}
		
		$xml = simplexml_load_string($path);
	}
}
