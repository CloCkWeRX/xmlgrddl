<?php
$tests[] = array('name' => 'P3P work-alike',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/xmlWithGrddlAttribute.xml',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/xmlWithGrddlAttribute-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/xmlWithGrddlAttribute-output.rdf');

$tests[] = array('name' => 'Get RDF from a spreadsheet',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/projects.xml',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/projects.rdf',
                 'realistic' => dirname(__FILE__) . '/data/projects.rdf');

$tests[] = array('name' => 'RDFa example',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/rdf_sem.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/rdf_sem-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/rdf_sem-output.rdf');

$tests[] = array('name' => 'Inline transformation reference',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/inline.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/inline.rdf',
                 'realistic' => dirname(__FILE__) . '/data/inline.rdf');

$tests[] = array('name' => 'Base URI: Same document reference',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/baseURI.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/baseURI.rdf',
                 'realistic' => dirname(__FILE__) . '/data/baseURI.rdf');

$tests[] = array('name' => 'Title / Author (from specification)',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/titleauthor.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/titleauthor-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/titleauthor-output.rdf');

/* //Skipping test: We'll never try to transform a document with no transformations
$tests[] = array('name' => 'RDF/XML document',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/projects.rdf',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/projects.rdf',
                 'realistic' => dirname(__FILE__) . '/data/projects.rdf');
*/

$tests[] = array('name' => 'One transform linked from the head of a document with only the GRDDL profile',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/xhtmlWithGrddlProfile.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/xhtmlWithGrddlProfile-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/xhtmlWithGrddlProfile-output.rdf');


$tests[] = array('name' => 'One transform linked from the body of a document with only the GRDDL profile',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/xhtmlWithGrddlTransformationInBody.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/xhtmlWithGrddlTransformationInBody-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/xhtmlWithGrddlTransformationInBody-output.rdf');


$tests[] = array('name' => 'One transform linked from the head of a document with several profiles, including the GRDDL profile',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/xhtmlWithMoreThanOneProfile.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/xhtmlWithTwoTransformations-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/xhtmlWithTwoTransformations-output.rdf');


$tests[] = array('name' => 'Two transformations linked from the body of a document with the GRDDL profile',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/xhtmlWithMoreThanOneGrddlTransformation.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/xhtmlWithTwoTransformations-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/xhtmlWithTwoTransformations-output.rdf');


$tests[] = array('name' => 'XML document linking to its transformer through the GRDDL attribute',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/xmlWithGrddlAttributeAndNonXMLNamespaceDocument.xml',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/xmlWithGrddlAttributeAndNonXMLNamespaceDocument-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/xmlWithGrddlAttributeAndNonXMLNamespaceDocument-output.rdf');
