<?php
class XML_GRDDL {
	const NS = "http://www.w3.org/2003/g/data-view#";
	const XHTML_NS = 'http://www.w3.org/1999/xhtml';

	public static function factory($driver = 'xsl', $options = array('documentTransformations' => true, 'htmlTransformations' => true)) {
		$class = 'XML_GRDDL_Driver_' . $driver;
		
		$path = dirname(__FILE__) . '/GRDDL/Driver/' . $driver . '.php';

		if (file_exists($path)) {
			require_once $path;
		}

		if (!class_exists($class)) {
			throw new Exception("Unknown driver " . $class);
		}

		return new $class($options);
	}
}