<?php
require_once 'HTTP/Request.php';

abstract class XML_GRDDL_Driver {

    protected $options;

    public function __construct($options = array()) {
        $this->options = $options;
    }

    public function inspect($xml, $original_url = null) {
        $sxe = simplexml_load_string($xml);
        if (!$sxe instanceOf SimpleXMLElement) {
            throw new Exception("Failed to parse xml");
        }

        $sxe->registerXPathNamespace('grddl', XML_GRDDL::NS);

        $transformations = array();
        if ($this->options['htmlTransformations']) {
            $transformations = array_merge($transformations, $this->_discoverHTMLTransformations($sxe, $original_url));
        }

        if ($this->options['htmlProfileTransformations']) {
            $transformations = array_merge($transformations, $this->_discoverHTMLProfileTransformations($sxe, $original_url));
        }

        if ($this->options['documentTransformations']) {
            $transformations = array_merge($transformations, $this->_discoverDocumentTransformations($sxe, $original_url));
        }
        if ($this->options['namespaceTransformations']) {
            $transformations = array_merge($transformations, $this->_discoverNamespaceTransformations($sxe, $original_url));
        }

        return $transformations;
    }

    protected function _discoverTransformations(SimpleXMLElement $sxe, $original_url = null, $xpath, $attribute_name, $namespace = null) {
        $nodes = $sxe->xpath($xpath);

        $dom = new DOMDocument('1.0');
        $dom_sxe = dom_import_simplexml($sxe);
        $dom_sxe = $dom->importNode($dom_sxe, true);
        $dom_sxe = $dom->appendChild($dom_sxe);

        $transformation_urls = array();
        foreach ($nodes as $node) {
            $attributes = $node->attributes($namespace);
            $value      = (string)$attributes[$attribute_name];
            $urls       = explode(" ", $value);

            foreach ($urls as $n => $url) {
                if (!$this->isURI($url)) {
                    $urls[$n] = $this->_determineBaseURI($dom, $original_url) . $url;
                }
            }

            $transformation_urls = array_merge($transformation_urls, $urls);
        }

        return $transformation_urls;
    }

    /**
     * Look for transformations hidden in A, LINK tag.
     *
     * @return  string[]    An array of XSL transformation urls.
     */
    protected function _discoverHTMLTransformations(SimpleXMLElement $sxe, $original_url = null) {

        $sxe->registerXPathNamespace('xhtml', XML_GRDDL::XHTML_NS);

        $transformation_urls = $this->_discoverTransformations($sxe, $original_url, "//xhtml:*[contains(@rel, 'transformation')]", 'href');

        return $transformation_urls;
    }

    /**
     * Look for profileTransformations (via PROFILE tags).
     *
     * @todo    Determine if I need to make //xhtml:head[@profile] softer for HTML 4
     * @todo    Determine if I need to make //xhtml:head[@profile] behave like a namespace transformation (I think I might?)
     */
    protected function _discoverHTMLProfileTransformations(SimpleXMLElement $sxe, $original_url = null) {

        $sxe->registerXPathNamespace('xhtml', XML_GRDDL::XHTML_NS);

        //Todo: Ensure this actually works as expected
        $profile_urls = $this->_discoverTransformations($sxe, $original_url, "//xhtml:head[@profile]", 'profile');

        //Todo: extract to _knownHTMLProfileTransformations()?
        $profile_transformation_urls = array();
        foreach ($profile_urls as $profile_url) {

            try {
                $xhtml = $this->fetch($profile_url);
            } catch (Exception $e) {
                //Emit log warning?
                continue;
            }

            $profile = @simplexml_load_string($xhtml);

            if ($profile instanceOf SimpleXMLElement) {
                $profile->registerXPathNamespace('xhtml', XML_GRDDL::XHTML_NS);
                $profile_transformations = $this->_discoverTransformations($profile, $profile_url, "//xhtml:*[contains(@rel, 'profileTransformation')]", 'href');
                $profile_transformation_urls = array_merge($profile_transformation_urls, $profile_transformations);
            }

        }

        return $profile_transformation_urls;
    }

    /**
     * Fetch a URL, which should be a namespace document of some description.
     * Look for namespaceTransformations
     *
     * @param   string  $ns_url Namespace URL
     * @todo    Check a cache of some description
     */
    protected function _knownNamespaceTransformations($ns_url) {
        $transformation_urls = array();

        $xml = $this->fetch($ns_url);
        $namespace = @simplexml_load_string($xml);

        if ($namespace instanceOf SimpleXMLElement) {
            $namespace->registerXPathNamespace('grddl', XML_GRDDL::NS);

            $transformation_urls = $this->_discoverTransformations($namespace, $ns_url, "//*[@grddl:namespaceTransformation]",
                                                                        'namespaceTransformation', XML_GRDDL::NS);
        }
        return $transformation_urls;
    }

    /**
     * Inspect a DOMDocument and kludge together a base URI.
     *
     * Otherwise, try to use the existing original document location.
     *
     * @return  string
     */
    protected function _determineBaseURI(DOMDocument $dom, $original_url) {
        if (!empty($dom->baseURI)) {
            return $dom->baseURI . '/';
        }

        return dirname($original_url) . '/';
    }

    /**
     * Given an XPath[XPATH] root node N with root element E, if the expression
     *
     * @*[local-name()="transformation"
     *    and namespace-uri()=
     *      "http://www.w3.org/2003/g/data-view#"]
     *
     * matches an attribute of an element E, then for each space-separated token REF in the value of that attribute, the resource identified[WEBARCH]
     & by the absolute form (see section 5.2 Relative Resolution in [RFC3986]) of REF with respect to the base IRI[RFC3987],[XMLBASE] of E
     * is a GRDDL transformation of N.
     *
     * Space-separated tokens are the maximal non-empty subsequences not containing the whitespace characters #x9, #xA, #xD or #x20.
     */
    protected function _discoverDocumentTransformations(SimpleXMLElement $sxe, $original_url = null) {
        $nodes = $sxe->xpath("//*[@grddl:transformation]");

        return $this->_discoverTransformations($sxe, $original_url, "//*[@grddl:transformation]", 'transformation', XML_GRDDL::NS);
    }

    /**
     * Transformations can be associated not only with individual documents but also with whole dialects that share an XML namespace.
     * Any resource available for retrieval from a namespace URI is a namespace document (cf. section 4.5.4. Namespace documents in [WEBARCH]).
     * For example, a namespace document may have an XML Schema representation or an RDF Schema representation, or perhaps both, using content negotiation.
     *
     * To associate a GRDDL transformation with a whole dialect, include a grddl:namespaceTransformation property in a GRDDL result of the namespace document.
     */
    protected function _discoverNamespaceTransformations(SimpleXMLElement $sxe) {
        //List all namespace urls
        $namespaces = $sxe->getNamespaces(true);

        $transformation_urls = array();


        foreach ($namespaces as $ns_url) {
            //Retrieve or check a local cache for $ns_url
            $urls = $this->_knownNamespaceTransformations($ns_url);
            $transformation_urls = array_merge($transformation_urls, $urls);
        }

        return $transformation_urls;
    }

    /**
     * Inspect a string to see if it is a valid URL
     *
     * @return  bool
     */
    public function isURI($string) {
        $url_pattern = '([A-Za-z][A-Za-z0-9+.-]{1,120}:[A-Za-z0-9/](([A-Za-z0-9$_.+!*,;/?:@&~=-])|%[A-Fa-f0-9]{2}){1,333}(#([a-zA-Z0-9][a-zA-Z0-9$_.+!*,;/?:@&~=%-]{0,1000}))?)';
        return (bool)preg_match($url_pattern, $string);
    }

    /**
     * Transform the given XML with the provided XSLT.
     *
     * Driver implementations should override this method.
     *
     * @param   string  $stylesheet URL or file location of an XSLT transformation
     * @param   string  $xml        String of XML
     */
    abstract public function transform($stylesheet, $xml);

    /**
     * Fetch a URL, specifically asking for XML or RDF where available.
     *
     * @throws  Exception   Unable to fetch url or file
     *
     * @bug     Deal with error response codes to exceptions
     * @bug     Deal with ambigious reponse codes (300)
     */
    public function fetch($path) {

        if ($this->isURI($path)) {
            $req = &new HTTP_Request($path);
            $req->setMethod(HTTP_REQUEST_METHOD_GET);
            $req->addHeader("Accept", 'text/xml, application/xml, application/rdf+xml; q=0.9, */*; text/html q=0.1');
            $req->sendRequest();

            //HTTP 200 OK
            if ($req->getResponseCode() == 200) {
                return $req->getResponseBody();
            }

            //HTTP 301 - UH...
            if ($req->getResponseCode() == 301) {
                //For now, return response body, otherwise, consider following redirect?
                return $req->getResponseBody();
            }

            //w3c.org website hacky workarounds
            //ewwwww
            if ($req->getResponseCode() == 300) {
                return $this->fetch($path . '.html');
            }




            throw new Exception('HTTP ' . $req->getResponseCode() . ' while retrieving ' . $path);
        }

        if (file_exists($path)) {
            return file_get_contents($path);
        }

        throw new Exception("Unable to fetch " . $path);
    }

    /**
     * Merge two GRDDL results into one.
     *
     * If F and G are GRDDL results of IR, then the merge [RDF-MT] of F and G is also a GRDDL result of IR.
     *
     * ?IR grddl:result ?F, ?G.
     * (?F ?G) log:conjunction ?H.
     *
     *  ?IR grddl:result ?H.
     *
     * @bug This method does not check for duplicate nodeIDs
     *
     * @see http://www.w3.org/2004/01/rdxh/spec
     * @see http://www.w3.org/TR/2004/REC-rdf-mt-20040210/#defmerge
     *
     * @param   string  $graph_xml1 An RDF/XML graph
     * @param   string  $graph_xml2 A second RDF/XML graph, to be merged into the first.
     *
     * @return  string  Merged graph containing triples from both original graphs
     */
    public function merge($graph_xml1, $graph_xml2) {
        if (empty($graph_xml1)) {
            return $graph_xml2;
        }

        $dom1 = new DomDocument();
        $dom2 = new DomDocument();

        $dom1->loadXML($graph_xml1);
        $dom2->loadXML($graph_xml2);

        // pull all child elements of second XML
        $xpath = new DomXPath($dom2);
        $xpathQuery = $xpath->query('/*/*');

        for ($i = 0; $i < $xpathQuery->length; $i++) {
            // and pump them into first one
            $dom1->documentElement->appendChild(
            $dom1->importNode($xpathQuery->item($i), true));
        }

        return $dom1->saveXML();
    }

    /**
     * Fetch, inspect, parse and merge a URL.
     *
     * If you just want to get RDF, and you want to get it now...
     *
     * @param   $url    Address of document to crawl.
     * @return  string  Resulting RDF document
     */
    public function crawl($url) {
        $data        = $this->fetch($url);
        $stylesheets = $this->inspect($data, $url);

        $rdf_xml = array();
        foreach ($stylesheets as $stylesheet) {
            $rdf_xml[] = $this->transform($stylesheet, $data);
        }

        $result = array_reduce($rdf_xml, array($this, 'merge'));

        return $result;
    }
}
