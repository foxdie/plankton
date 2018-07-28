<?php

namespace Plankton\Server;

use Symfony\Component\Yaml\Yaml;
use Plankton\Request;


class Config{
	/**
	 * @access private
	 * @var string
	 */
	private $path;
	
	/**
	 * @access private
	 * @var Route[]
	 */
	private $routes;
	
	/**
	 * @access private
	 * @var Controller[]
	 */
	private $controllers;
	
	/**
	 * @access public
	 * @param string $path
	 */
	public function __construct(string $path){
		$this->path = $path;
		$this->controllers = [];
		$this->routes = [];
		
		$this->parse();
	}
	
	/**
	 * @access public
	 * @return Controller[]
	 */
	public function getControllers(): array{
		return $this->controllers;
	}
	
	/**
	 * @access public
	 * @param Controller $controller
	 * @return Config
	 */
	public function applyTo(Controller $controller): Config{		
		foreach ($this->routes as $name => $route) {
			list($class, $method) = explode("::", $route["controller"]);
			
			if ($controller instanceof $class) {
				$route = new Route($route["path"], $route["method"]);
				$controller->addRoute($route, [$controller, $method]);
			}
		}
		
		return $this;
	}
	
	/**
	 * @access private
	 * @return void
	 */
	private function parse(): void{
		$config = Yaml::parseFile($this->path);
		
		$this
			->setRoutes($config["routes"] ?? [])
			->setControllers($config["routes"] ?? []);
	}
	
	/**
	 * @ccess private
	 * @param array[] $routes
	 * @return Config
	 */
	private function setRoutes(array $routes): Config{
		foreach ($routes as $name => $route) {
			if (!isset($route["path"]) || !isset($route["method"])) {
				throw new \RuntimeException("Error in {$this->path}: the route '$name' must have a 'path' and a 'method' defined");
			}
			
			switch ($route["method"]) {
				case Request::METHOD_DELETE:
				case Request::METHOD_GET:
				case Request::METHOD_PATCH:
				case Request::METHOD_POST:
				case Request::METHOD_PUT:
					$this->routes[$name] = $route;
					break;
				default:
					throw new \RuntimeException("Error in {$this->path}: unknown method '{$route["method"]} for route '$name'");
			}
			
		}
		
		return $this;
	}
	
	/**
	 * @ccess private
	 * @param array[] $routes
	 * @return Config
	 */
	private function setControllers(array $routes): Config{
		$classes = [];
		
		foreach ($routes as $name => $route) {
			if (!isset($route["controller"]) || !preg_match("#^[^:]+::[^:]+\$#", $route["controller"])) {
				throw new \RuntimeException("Error in {$this->path}: the route '$name' must have a valid 'controller' defined");
			}
			
			list($class, $method) = explode("::", $route["controller"]);
			
			if (!class_exists($class)) {
				//throw new \RuntimeException("Error in {$this->path}: unknown controller '$class' for route '$name'");
			}
		
			$classes[] = $class;
		}
		
		foreach (array_unique($classes) as $class) {
			$controller = new $class();
			
			if (!$controller instanceof Controller) {
				throw new \RuntimeException("Error in {$this->path}: the controller '$class' must extends \\Plankton\\Server\\Controller");
			}
			
			$this->controllers[] = $controller;
		}
		
		return $this;
	}
}
