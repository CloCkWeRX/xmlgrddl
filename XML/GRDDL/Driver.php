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
		if ($this->options['documentTransformations']) {
			$transformations = array_merge($transformations, $this->_discoverDocumentTransformations($sxe, $original_url));
		}
		if ($this->options['namespaceTransformations']) {
			$transformations = array_merge($transformations, $this->_discoverNamespaceTransformations($sxe, $original_url));
		}

		return $transformations;
	}

	protected function _discoverHTMLTransformations(SimpleXMLElement $sxe, $original_url = null) {
		
		$sxe->registerXPathNamespace('xhtml', XML_GRDDL::XHTML_NS);

		$query = "//xhtml:*[contains(@rel, 'transformation')]";
		$nodes = $sxe->xpath($query);

        $dom = new DOMDocument('1.0');
        $dom_sxe = dom_import_simplexml($sxe);
        $dom_sxe = $dom->importNode($dom_sxe, true);
        $dom_sxe = $dom->appendChild($dom_sxe);

        $transformation_urls = array();
        foreach ($nodes as $node) {
            $attributes = $node->attributes();
            $value      = (string)$attributes['href'];
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
     * @todo    Check a cache of some description
     */
    protected function _knownNamespaceTransformations($original_url) {
        $transformation_urls = array();

		$xml = $this->fetch($original_url);
        $namespace = @simplexml_load_string($xml);

        if ($namespace instanceOf SimpleXMLElement) {
            $dom = new DOMDocument('1.0');
            $dom_sxe = dom_import_simplexml($namespace);
            $dom_sxe = $dom->importNode($dom_sxe, true);
            $dom_sxe = $dom->appendChild($dom_sxe);

            $namespace->registerXPathNamespace('grddl', XML_GRDDL::NS);
            $nodes = $namespace->xpath("//*[@grddl:namespaceTransformation]");

            foreach ($nodes as $node) {
                $attributes = $node->attributes(XML_GRDDL::NS);
                $value      = (string)$attributes['namespaceTransformation'];
                $urls       = explode(" ", $value);

                //Todo: see if this is ever executed in test cases
                foreach ($urls as $n => $url) {
                    if (!$this->isURI($url)) {
                        $urls[$n] = $this->_determineBaseURI($dom, $orginal_url) . $url;
                    }
                }

                $transformation_urls = array_merge($transformation_urls, $urls);
            }
        }
        return $transformation_urls;
    }

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

        $dom = new DOMDocument('1.0');
        $dom_sxe = dom_import_simplexml($sxe);
        $dom_sxe = $dom->importNode($dom_sxe, true);
        $dom_sxe = $dom->appendChild($dom_sxe);

        $transformation_urls = array();
        foreach ($nodes as $node) {
            $attributes = $node->attributes(XML_GRDDL::NS);
            $value      = (string)$attributes['transformation'];
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
     * Transformations can be associated not only with individual documents but also with whole dialects that share an XML namespace. Any resource available for retrieval from a namespace URI is a namespace document (cf. section 4.5.4. Namespace documents in [WEBARCH]). For example, a namespace document may have an XML Schema representation or an RDF Schema representation, or perhaps both, using content negotiation.
     *
     * To associate a GRDDL transformation with a whole dialect, include a grddl:namespaceTransformation property in a GRDDL result of the namespace document.
     */
    protected function _discoverNamespaceTransformations($sxe) {
        //List all namespaces
        $namespaces = $sxe->getNamespaces(true);

        $transformation_urls = array();

        //Retrieve or check a local cache
        foreach ($namespaces as $prefix => $ns) {
            $urls = $this->_knownNamespaceTransformations($ns);
            $transformation_urls = array_merge($transformation_urls, $urls);
        }

        return $transformation_urls; 
    }

	/**
	 * Inspect a string to see if it is a valid URL
	 *
	 * @return	bool
	 */
    public function isURI($string) {
        $url_pattern = '/([A-Za-z][A-Za-z0-9+.-]{1,120}:[A-Za-z0-9/](([A-Za-z0-9$_.+!*,;/?:@&~=-])|%[A-Fa-f0-9]{2}){1,333}(#([a-zA-Z0-9][a-zA-Z0-9$_.+!*,;/?:@&~=%-]{0,1000}))?)/';
        return (bool)preg_match($url_pattern, $string);
    }

	/**
	 * Transform the given XML with the provided XSLT
	 *
	 * @param	string	$stylesheet	URL or file location of an XSLT transformation	
	 * @param	string	$xml		String of XML
	 */
	abstract public function transform($stylesheet, $xml);

	
	public function fetch($path) {
		
		if ($this->isURI($path)) {
			$req = &new HTTP_Request($path);
			$req->setMethod(HTTP_REQUEST_METHOD_GET);
			$req->addHeader("Accept", 'text/xml, application/xml, application/rdf+xml; q=0.9, */*; q=0.1');
			$req->sendRequest();
			return $req->getResponseBody();
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
	 * @see	http://www.w3.org/2004/01/rdxh/spec
	 * @see	http://www.w3.org/TR/2004/REC-rdf-mt-20040210/#defmerge
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
	 * @param	$url	Address of document to crawl.
	 */
	public function crawl($url) {
		$data		 = $this->fetch($url);
		$stylesheets = $this->inspect($data, $url);

		$rdf_xml = array();
		foreach ($stylesheets as $stylesheet) {
			$rdf_xml[] = $this->transform($stylesheet, $data);
		}

		$result = array_reduce($rdf_xml, array($this, 'merge'));

		return $result;
	}
}
