<?php
require_once 'XML/GRDDL.php';
require_once 'Log.php';

$logger = Log::singleton('null');

$file = $_SERVER['argv'][1];

$options = array('documentTransformations' => true,
                    'htmlTransformations' => true,
                    'htmlProfileTransformations' => true,
                    'namespaceTransformations' => true,
                    'preserveWhiteSpace' => false,
                    'formatOutput' => true,
                    'log' => $logger,
                    'quiet' => true);

$grddl = XML_GRDDL::factory('xsl', $options);

print $grddl->crawl($file);


//python testft.py -r "php process-grddl.php " --debug grddl-tests.rdf > results.rdf
