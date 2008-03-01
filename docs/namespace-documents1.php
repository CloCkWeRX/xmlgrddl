<?php
require_once 'XML/GRDDL.php';

/**
 * Namespace documents and media types 1
 *
 * Compare the results to http://www.w3.org/2001/sw/grddl-wg/td/sq1-output.rdf
 */

$url = 'http://www.w3.org/2001/sw/grddl-wg/td/sq1.xml';

//Set what kind of transformations we're interested in.
$options = array('documentTransformations' => true,     // XML
                 'namespaceTransformations' => true,    // XML namespaces
                 'htmlTransformations' => true,         // HTML <link> transforms
                 'htmlProfileTransformations' => true); // HTML Profile transform

$grddl = XML_GRDDL::factory('xsl', $options);

var_dump($grddl->crawl($url));
