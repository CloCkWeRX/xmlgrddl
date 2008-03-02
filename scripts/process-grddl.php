<?php
require_once 'XML/GRDDL.php';

$file = $_SERVER['argv'][1];

$options = array('documentTransformations' => true,     //For dealing with XML
                 'namespaceTransformations' => true,    //For dealing with XML namespaces
                 'htmlTransformations' => true,         //For dealing with HTML <link> transformations
                 'htmlProfileTransformations' => true,  //For dealing with HTML Profile transformations
                 'library' => true);

$grddl = XML_GRDDL::factory('xsl', $options);

print $grddl->crawl($file);