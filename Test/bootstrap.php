<?php

class Autoloader{
	public static function load($className) {
		$filename = realpath(__DIR__ . "/../" . str_replace("\\", '/', $className) . ".php");
		
		if (file_exists($filename)) {
			include_once($filename);
			if (class_exists($className)) {
				return true;
			}
		}
		
		//throw new Exception($className . " not found");
		return false;
	}
}

spl_autoload_register("Autoloader::load");
