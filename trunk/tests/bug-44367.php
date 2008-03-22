<?php
$url = 'http://www.w3.org/2001/sw/grddl-wg/td/base/xmlWithBase.xml';
$xml = file_get_contents($url);

//Load a url
//Load an xml document with xml:base
$doc = DOMDocument::load($url);
var_dump($doc->baseURI);                      //Expected http://www.w3.org/2001/sw/grddl-wg/td/base/xmlWithBase.xml
var_dump($doc->documentElement->baseURI);     //Expected http://www.w3.org/2001/sw/grddl-wg/td/base/xmlWithBase

//Does it work with importNode?
$sxe = simplexml_load_file($url);
$dom_sxe = dom_import_simplexml($sxe);

$dom = new DOMDocument('1.0');
$dom_sxe = $dom->importNode($dom_sxe, true);
$dom_sxe = $dom->appendChild($dom_sxe);
var_dump($doc->baseURI);                     //Expected http://www.w3.org/2001/sw/grddl-wg/td/base/xmlWithBase.xml
var_dump($doc->documentElement->baseURI);    //Expected http://www.w3.org/2001/sw/grddl-wg/td/base/xmlWithBase

// Alternative?
$sxe = simplexml_load_string($xml);
$dom_sxe = dom_import_simplexml($sxe);

$dom = new DOMDocument('1.0');
$dom_sxe = $dom->importNode($dom_sxe, true);
$dom_sxe = $dom->appendChild($dom_sxe);
var_dump($doc->baseURI);                     //Expected http://www.w3.org/2001/sw/grddl-wg/td/base/xmlWithBase.xml
var_dump($doc->documentElement->baseURI);    //Expected http://www.w3.org/2001/sw/grddl-wg/td/base/xmlWithBase


//What about documents with an invalid xml:base (not on the top level element)?
$doc = DOMDocument::load('http://www.w3.org/2001/sw/grddl-wg/td/inline-rdf6.xml');
var_dump($doc->baseURI);                     //Expected http://www.w3.org/2001/sw/grddl-wg/td/inline-rdf6.xml
var_dump($doc->documentElement->baseURI);    //Expected http://wwww.example.org/

//What about documents with a *redirected xml:base* ?
//Note: this test case is a little broken because of a W3C server change - it *should* redirect to 'http://www.w3.org/2001/sw/grddl-wg/td/base/xmlWithBase.xml'
//      and thus have a funky new xml:base value
$doc = DOMDocument::load('http://www.w3.org/2001/sw/grddl-wg/td/xmlWithBase');
var_dump($doc->baseURI);                     //Expected http://www.w3.org/2001/sw/grddl-wg/td/xmlWithBase.xml
var_dump($doc->documentElement->baseURI);    //Expected http://www.w3.org/2001/sw/grddl-wg/td/xmlWithBase

//systembot
//readyornot