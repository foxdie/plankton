<?php

/**
 * Simple autoloader
 */
class Autoloader{
	/**
	 * @access public
	 * @static
	 * @param string $className
	 * @return boolean
	 */
	public static function load(string $className): bool{
		$filename = realpath(__DIR__ . "/../" . str_replace("\\", '/', $className) . ".php");
		
		if (file_exists($filename)) {
			include_once($filename);
			if (class_exists($className)) {
				return true;
			}
		}
		
		return false;
	}
}

spl_autoload_register("Autoloader::load");
