<?php
$tests[] = array('name' => 'An hcard profile',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/card.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/card-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/card-output.rdf');

$tests[] = array('name' => '2 profiles: eRDF and hCard',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/multiprofile.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/multiprofile-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/multiprofile-output.rdf');

$tests[] = array('name' => 'Namespace documents and media types 1',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/sq1.xml',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/sq1-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/sq1-output.rdf');

$tests[] = array('name' => 'Namespace documents and media types 2',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/sq2.xml',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/sq2-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/sq2-output.rdf');

$tests[] = array('name' => 'A variant of the card5n test',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/card5na.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/card5n-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/card5n-output.rdf');

$tests[] = array('name' => 'hcard from a 1998 review comment on P3P',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/card5n.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/card5n-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/card5n-output.rdf');

/** @todo   Determine just what the hell is correct behaviour */
/*
$tests[] = array('name' => 'A copy of the hcard profile',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/hcard.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/hcard-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/hcard-output.rdf');
*/

/** @bug issue 8 */
/*
$tests[] = array('name' => 'An XML document with two namespace transformations',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/two-transforms.xml',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/two-transforms-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/two-transforms-output.rdf');
*/

$tests[] = array('name' => 'An XML document with two namespace transformations and a transform on the root element',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/three-transforms.xml',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/three-transforms-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/three-transforms-output.rdf');

$tests[] = array('name' => 'An XML document with two namespace transformations and two transforms on the root element',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/four-transforms.xml',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/four-transforms-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/four-transforms-output.rdf');

/** @todo   Determine just what the hell is correct behaviour */
/*
$tests[] = array('name' => 'A variant of the hcard profile',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/hcarda.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/hcard-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/hcard-output.rdf');
*/

$tests[] = array('name' => 'Document linking to its transformer through a GRDDL-enabled profile',
                 'in'  => 'http://www.w3.org/2001/sw/grddl-wg/td/xhtmlWithGrddlEnabledProfile.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/xhtmlWithGrddlEnabledProfile-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/xhtmlWithGrddlEnabledProfile-output.rdf');
