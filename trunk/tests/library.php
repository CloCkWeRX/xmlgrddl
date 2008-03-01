<?php
// Library tests
$tests[] = array('name' => 'Embedded RDF1',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/inline-rdf1.xml',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/embedded-rdf1-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/embedded-rdf1-output.rdf');

$tests[] = array('name' => 'Embedded RDF2',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/inline-rdf2.xml',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/embedded-rdf2-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/embedded-rdf2-output.rdf');

$tests[] = array('name' => 'Embedded RDF3',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/inline-rdf3.xml',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/embedded-rdf3-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/embedded-rdf3-output.rdf');

$tests[] = array('name' => 'Glean Profile',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/profile-with-spaces-in-rel.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/profile-with-spaces-in-rel-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/profile-with-spaces-in-rel-output.rdf');

$tests[] = array('name' => 'Embedded RDF using a relative xml:base',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/inline-rdf4.xml',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/embedded-rdf4-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/embedded-rdf4-output.rdf');

$tests[] = array('name' => 'Embedded RDF using an absolute xml:base',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/inline-rdf5.xml',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/embedded-rdf5-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/embedded-rdf5-output.rdf');

$tests[] = array('name' => 'Embedded RDF using two nested absolute xml:base',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/inline-rdf6.xml',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/embedded-rdf6-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/embedded-rdf6-output.rdf');

$tests[] = array('name' => 'Embedded RDF using two different xml:base on two different blocks of RDF',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/inline-rdf8.xml',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/embedded-rdf8-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/embedded-rdf8-output.rdf');


$tests[] = array('name' => 'Embedded RDF using two different xml:lang on two different blocks of RDF',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/inline-rdf9.xml',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/embedded-rdf9-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/embedded-rdf9-output.rdf');

$tests[] = array('name' => 'An XHTML profile using a base element',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/base/grddlProfileWithBaseElement.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/grddlProfileWithBaseElement-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/embedded-rdf9-output.rdf');

$tests[] = array('name' => 'XHTML with an XHTML profile using a base element',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/xhtmlProfileBase1.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/xhtmlProfileBase1-output.rdf',
                 'realistic' => dirname(__FILE__) . '/data/embedded-rdf9-output.rdf');