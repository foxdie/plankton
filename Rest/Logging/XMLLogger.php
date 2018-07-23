<?php

namespace Rest\Logging;

use \SplObjectStorage;
use \SimpleXMLElement;
use Rest\Request;
use Rest\Response;


class XMLLogger implements Logger{
	/**
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
	public function getLogs(): SimpleXMLElement{
		$xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><requests></requests>");
		
		foreach ($this->logs as $request) {
			$response = $this->logs[$request];
			
			$requestNode = $xml->addChild("request");
			
			$requestNode->addChild("method", $request->getMethod());
			$requestNode->addChild("scheme", $request->getScheme());
			$requestNode->addChild("host", $request->getHost());
			$requestNode->addChild("uri", $request->getURI());
			
			// request parameters
			$parametersNode = $requestNode->addChild("parameters");
			
			foreach ($request->getParameters() as $name => $value) {
				$parameterNode = $parametersNode->addChild("parameter");
				$parameterNode->addChild("name", $name);
				$parameterNode->addChild("value", $value);
			}
			
			// request headers
			$requestHeadersNode = $requestNode->addChild("headers");
			
			foreach ($request->getHeaders() as $name => $value) {
				$requestHeaderNode = $requestHeadersNode->addChild("header");
				$requestHeaderNode->addChild("name", $name);
				$requestHeaderNode->addChild("value", $value);
			}
			
			// request data
			$data = $request->getData() ?: [];
			$dataNode = $requestNode->addChild("data", htmlentities(http_build_query($data)));
	
			// response
			$responseNode = $requestNode->addChild("response");
			
			$responseNode->addChild("code", $response->getCode());
			
			// response headers
			$responseHeadersNode = $responseNode->addChild("headers");
			
			foreach ($response->getHeaders() as $name => $value) {
				$responseHeaderNode = $responseHeadersNode->addChild("header");
				$responseHeaderNode->addChild("name", $name);
				$responseHeaderNode->addChild("value", $value);
			}
			
			//content
			$responseNode->addChild("content", $response->getContent());
		}
		
		return $xml;
	}
}
