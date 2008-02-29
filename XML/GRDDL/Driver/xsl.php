<?php
require_once 'XML/GRDDL/Driver.php';

class XML_GRDDL_Driver_xsl extends XML_GRDDL_Driver {
	public function __construct($options = array()) {
        if (!extension_loaded('xsl')) {
            throw new Exception("Don't forget to enable the xsl extension");
        }

		parent::__construct($options);
	}

	public function transform($stylesheet, $xml) {
        $dom = new DOMDocument('1.0');
		$dom->loadXML($xml);

        $xsl = new DOMDocument();
        $xsl->load($stylesheet);

        $proc = new XSLTProcessor();
        $proc->importStyleSheet($xsl);

        return $proc->transformToXML($dom);
	}
}