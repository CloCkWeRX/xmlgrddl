<?php
require_once 'XML/GRDDL.php';

/**
 * Example: Read the RDF calendar / event information 
 * from Dan Connolly's w3 homepage.
 *
 * Compare the results to http://www.w3.org/2000/06/webdata/xslt?xslfile=http%3A%2F%2Fwww.w3.org%2F2003%2F11%2Frdf-in-xhtml-processor&xmlfile=http%3A%2F%2Fwww.w3.org%2FPeople%2FConnolly%2F
 */

$url = 'http://www.w3.org/People/Connolly/';

//Set what kind of transformations we're interested in.
$options = array('documentTransformations' => true,		//For dealing with XML
				 'namespaceTransformations' => true,	//For dealing with XML namespaces
				 'htmlTransformations' => true);		//For dealing with HTML <link> transformations

$grddl = XML_GRDDL::factory('xsl', $options);

var_dump($grddl->crawl($url));
    