<?php
/**
 * @bug http://bugs.php.net/bug.php?id=44489
 * @bug http://code.google.com/p/xmlgrddl/issues/detail?id=20
 */
/*
phpinfo();

xsl

XSL => enabled
libxslt Version => 1.1.22
libxslt compiled against libxml Version => 2.6.31
EXSLT => enabled
libexslt Version => 0.8.13
*/

$xsl = new DOMDocument();
$xsl->load('http://www.w3.org/2001/sw/grddl-wg/td/hl7-rim-to-pomr.xslt');


$xml = new DOMDocument();
$xml->load('http://www.w3.org/2001/sw/grddl-wg/td/hl7-sample.xml');

$proc = new XSLTProcessor();
$proc->importStyleSheet($xsl);

$result = $proc->transformToXML($xml);

var_dump($result);
/*
---------- PHP ----------
phpinfo()
PHP Version => 5.2.6-dev

bool(false)
PHP Warning:  XSLTProcessor::importStylesheet(): compilation error: file http://www.w3.org/2001/sw/grddl-wg/td/hl7-rim-to-pomr.xslt line 179 element type in G:\work\xml_grddl\tests\bug-h17.php on line 13
PHP Warning:  XSLTProcessor::importStylesheet(): Attribute 'resource': The content is expected to be a single text node when compiling an AVT. in G:\work\xml_grddl\tests\bug-h17.php on line 13
PHP Warning:  XSLTProcessor::importStylesheet(): compilation error: file http://www.w3.org/2001/sw/grddl-wg/td/hl7-rim-to-pomr.xslt line 200 element type in G:\work\xml_grddl\tests\bug-h17.php on line 13
PHP Warning:  XSLTProcessor::importStylesheet(): Attribute 'resource': The content is expected to be a single text node when compiling an AVT. in G:\work\xml_grddl\tests\bug-h17.php on line 13
PHP Warning:  XSLTProcessor::importStylesheet(): compilation error: file http://www.w3.org/2001/sw/grddl-wg/td/hl7-rim-to-pomr.xslt line 208 element type in G:\work\xml_grddl\tests\bug-h17.php on line 13
PHP Warning:  XSLTProcessor::importStylesheet(): Attribute 'resource': The content is expected to be a single text node when compiling an AVT. in G:\work\xml_grddl\tests\bug-h17.php on line 13
PHP Warning:  XSLTProcessor::transformToXml(): No stylesheet associated to this object in G:\work\xml_grddl\tests\bug-h17.php on line 15

*/
/*
Works with...
G:\libxml2-2.6.30+.win32\bin>xsltproc.exe http://www.w3.org/2001/sw/grddl-wg/td/hl7-rim-to-pomr.xslt http://www.w3.org/2001/sw/grddl-wg/td/hl7-sample.
xml

xsltproc --version
Using libxml 20630, libxslt 10122 and libexslt 813
xsltproc was compiled against libxml 20630, libxslt 10122 and libexslt 813
libxslt 10122 was compiled against libxml 20630
libexslt 813 was compiled against libxml 20630

*/

/*

G:\work\xml_grddl\scripts>php -v
PHP 5.2.6RC3-dev (cli) (built: Mar 20 2008 08:04:52)
Copyright (c) 1997-2008 The PHP Group
Zend Engine v2.2.0, Copyright (c) 1998-2008 Zend Technologies

G:\work\xml_grddl\scripts>
*/