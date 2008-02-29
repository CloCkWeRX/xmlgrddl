<?php
//http://bugs.php.net/bug.php?id=44225

$xml = "http://www.w3.org/2001/sw/grddl-wg/td/xhtmlWithMoreThanOneGrddlTransformation.html";
$stylesheet = "http://www.w3.org/2001/sw/grddl-wg/td/getAuthor.xsl";

$dom = new DOMDocument('1.0');
$dom->load($xml);

$xsl = new DOMDocument();
$xsl->load($stylesheet);

$proc = new XSLTProcessor();
$proc->importStyleSheet($xsl);


print $proc->transformToXML($dom);


phpinfo(8);

/*
C:\>xsltproc http://www.w3.org/2001/sw/grddl-wg/td/getAuthor.xsl http://www.w3.org/2001/sw/grddl-wg/td/xhtmlWithMoreThanOneGrddlTransformation.html

<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:foaf="http://xmlns.com/foaf/0.1/">
  <rdf:Description rdf:about="">
    <dc:creator xmlns:dc="http://purl.org/dc/elements/1.1/" rdf:parseType="Resource">
      <foaf:homepage rdf:resource="http://www.w3.org/People/Dom/"/>
    </dc:creator>
  </rdf:Description>
</rdf:RDF>


C:\>xsltproc --version
Using libxml 20630, libxslt 10122 and libexslt 813
xsltproc was compiled against libxml 20630, libxslt 10122 and libexslt 813
libxslt 10122 was compiled against libxml 20630
libexslt 813 was compiled against libxml 20630
*/