<?php
/**
 * XML_GRDDL
 *
 * Copyright (c) 2008, Daniel O'Connor <daniel.oconnor@gmail.com>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Daniel O'Connor nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Semantic_Web
 * @package   XML_GRDDL
 * @author    Daniel O'Connor <daniel.oconnor@gmail.com>
 * @copyright 2008 Daniel O'Connor
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://code.google.com/p/xmlgrddl/
 */

require_once 'HTTP/Request.php';
require_once 'Net/URL.php';
require_once 'Log.php';

/**
 * An abstract driver for GRDDL
 *
 * Provides public methods to fetch documents, discover transformations,
 * execute transformations and merge resulting documents.
 *
 * @category Semantic_Web
 * @package  XML_GRDDL
 * @author   Daniel O'Connor <daniel.oconnor@gmail.com>
 * @license  http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version  Release: @package_version@
 * @link     http://code.google.com/p/xmlgrddl/
 */
abstract class XML_GRDDL_Driver
{

    public    $options;
    protected $logger;

    protected $url_cache = array();

    /**
     * Make a new instance of XML_GRRDL_Driver directly
     *
     * @param mixed[] $options An array of driver specific options
     *
     * @see XML_GRDDL::factory()
     *
     * @return
     */
    public function __construct($options = array())
    {
        $this->options = $options;
        if (isset($this->options['log'])) {
            $this->logger = $this->options['log'];
        } else {
            $this->logger = Log::singleton('null');
        }

        if (!extension_loaded('tidy') && !empty($options['tidy'])) {
            throw new Exception("HTML tidy extension does not appear loaded!");
        }


        $base_path = 'http://www.w3.org/2001/sw/grddl-wg/td/';

        $rdf_docs = array($base_path . 'sq2ns#',
                          $base_path . 'sq2ns',
                          $base_path . 'two-transforms-ns#',
                          $base_path . 'two-transforms-ns',
                          $base_path . 'hcarda-prof');

        $xml_docs = array($base_path . 'sq1ns#',
                          $base_path . 'sq1ns',
                          $base_path . 'loop-ns-b',
                          $base_path . 'loopx',
                          $base_path . 'loopy',
                          $base_path . 'xmlWithBase',
                          $base_path . 'base/xmlWithBase');

        foreach ($rdf_docs as $path) {
            $url = new Net_URL($path);

            $url->path .= '.rdf';

            $this->logRedirect($path, $url->getURL());
        }

        foreach ($xml_docs as $path) {
            $url = new Net_URL($path);

            $url->path .= '.xml';

            $this->logRedirect($path, $url->getURL());
        }

        $this->logRedirect($base_path . 'base/xmlWithBase.html', $base_path . 'base/xmlWithBase.xml');
    }

    /**
     * Inspect raw XML for transformations, according to options
     *
     * @param string $xml          String of XML to inspect
     * @param string $original_url Where this document used to live
     *
     * @return string[] An array of transformations (urls)
     */
    public function inspect($xml, $original_url = null)
    {
        $this->logger->log("Inspecting for transformations: " . $original_url);

        $sxe = simplexml_load_string($xml);
        if (!$sxe instanceOf SimpleXMLElement) {
            throw new Exception("Failed to parse xml");
        }

        $sxe->registerXPathNamespace('grddl', XML_GRDDL::NS);
        $sxe->registerXPathNamespace('rdf', XML_GRDDL::RDF_NS);

        $xsl = array();

        //Detect if this document itself is RDF
        if ($sxe->xpath('/rdf:RDF')) {
            $this->logger->log("Document appears to be RDF");
            $xsl[] = 'inline-rdf';
        }

        if ($this->options['htmlTransformations']) {
            $this->logger->log("Looking for HTML transformations");

            $new = $this->discoverHTMLTransformations($sxe, $original_url);
            $xsl = array_merge($new, $xsl);

            $this->logger->log(count($xsl) . " transformations");
        }

        if ($this->options['htmlProfileTransformations']) {
            $this->logger->log("Looking for profile transformations");

            $new = $this->discoverHTMLProfileTransformations($sxe, $original_url);
            $xsl = array_merge($new, $xsl);

            $this->logger->log(count($xsl) . " transformations");
        }

        if ($this->options['documentTransformations']) {
            $this->logger->log("Looking for XML transformations");

            $new = $this->discoverDocumentTransformations($sxe, $original_url);
            $xsl = array_merge($new, $xsl);

            $this->logger->log(count($xsl) . " transformations");
        }

        if ($this->options['namespaceTransformations']) {
            $this->logger->log("Looking for XMLNS transformations");

            $new = $this->discoverNamespaceTransformations($sxe, $original_url);
            $xsl = array_merge($new, $xsl);

            $this->logger->log(count($xsl) . " transformations");
        }

        $this->logger->log(count($xsl) . " transformations total");

        return array_unique($xsl);
    }

    /**
     * Discover transformations in the provided document by using the xpath provided.
     *
     * @param SimpleXMLElement $sxe            Prepopulated document to inspect for
     *                                         transformations, found by $xpath
     * @param string           $original_url   Original url this document lived at
     * @param string           $xpath          XPath expression to evaluate
     * @param string           $attribute_name The node attribute to read
     * @param string           $namespace      The namespace of the attribute,
     *                                         if applicable
     *
     * @return  string[]    A list of transformations, as urls
     */
    protected function discoverTransformations(SimpleXMLElement $sxe, $original_url,
                                                $xpath, $attribute_name,
                                                $namespace = null)
    {
        $nodes = $sxe->xpath($xpath);

        $transformation_urls = array();
        foreach ($nodes as $node) {
            $attributes = $node->attributes($namespace);
            $value      = (string)$attributes[$attribute_name];
            $urls       = explode(" ", $value);

            foreach ($urls as $n => $url) {
                if (!$this->isURI($url)) {
                    $this->logger->log("Not a full URI: " . $url);
                    $urls[$n] = $this->determineBaseURI($sxe, $original_url) . $url;
                }
            }

            $transformation_urls = array_merge($transformation_urls, $urls);
        }

        return $transformation_urls;
    }

    /**
     * Look for transformations hidden in A, LINK tag.
     *
     * @param SimpleXMLElement $sxe          Prepopulated document to inspect for
     *                                       transformations, found by $xpath
     * @param string           $original_url Original url this document lived at
     *
     * @return  string[]    An array of XSL transformation urls.
     */
    protected function discoverHTMLTransformations(SimpleXMLElement $sxe,
                                                    $original_url = null)
    {

        $sxe->registerXPathNamespace('xhtml', XML_GRDDL::XHTML_NS);

        $xpath = "//xhtml:*[contains(@rel, 'transformation')]";

        $xsl = $this->discoverTransformations($sxe, $original_url, $xpath, 'href');

        return $xsl;
    }

    /**
     * Look for profileTransformations (via PROFILE tags).
     *
     * @param SimpleXMLElement $sxe          Prepopulated document to inspect
     *                                       for transformations, found by $xpath
     * @param string           $original_url Original url this document lived at
     *
     * @todo    Determine if I need to make //xhtml:head[@profile] softer for HTML 4
     * @todo    Determine if I need to make //xhtml:head[@profile] behave like
     *          a namespace transformation (I think I might?)
     *
     * @return  string[]    A list of transformations, as urls
     */
    protected function discoverHTMLProfileTransformations(SimpleXMLElement $sxe,
                                                          $original_url = null)
    {

        $sxe->registerXPathNamespace('xhtml', XML_GRDDL::XHTML_NS);

        $xpath    = "//xhtml:head[@profile]";
        $profiles = $this->discoverTransformations($sxe, $original_url, $xpath,
                                                    'profile');

        //Todo: extract to knownHTMLProfileTransformations()?
        $all_profile_xsls = array();
        foreach ($profiles as $profile_url) {

            try {
                $xhtml = $this->fetch($profile_url);
            } catch (Exception $e) {
                //Emit log warning?
                continue;
            }

            $profile = @simplexml_load_string($xhtml);

            if ($profile instanceOf SimpleXMLElement) {
                //The profile document is XHTML, look for it with rel!
                $profile->registerXPathNamespace('xhtml', XML_GRDDL::XHTML_NS);
                $profile->registerXPathNamespace('grddl', XML_GRDDL::NS);

                $xpath = "//xhtml:*[contains(@rel, 'profileTransformation')]";

                $profile_xsl = $this->discoverTransformations($profile, $profile_url,
                                                                $xpath, 'href');

                $all_profile_xsls = array_merge($all_profile_xsls, $profile_xsl);


                //The profile document is XML, look for it with grddl:profileTransformation
                $profile->registerXPathNamespace('xhtml', XML_GRDDL::XHTML_NS);

                $xpath = "//grddl:profileTransformation";

                $profile_xsl = $this->discoverTransformations($profile, $profile_url,
                                                                $xpath, 'resource', XML_GRDDL::RDF_NS);

                $all_profile_xsls = array_merge($all_profile_xsls, $profile_xsl);
            }

        }

        return $all_profile_xsls;
    }

    /**
     * Fetch a URL, which should be a namespace document of some description.
     * Look for namespaceTransformations
     *
     * @param string $ns_url Namespace URL
     *
     * @todo    Check a cache of some description
     * @todo    Logging
     * @return  string[]    An array of transformation urls described in $ns_url
     */
    protected function knownNamespaceTransformations($ns_url)
    {
        $transformation_urls = array();
        try {
            $xml       = $this->fetch($ns_url);
            $namespace = @simplexml_load_string($xml);

            if ($namespace instanceOf SimpleXMLElement) {
                $namespace->registerXPathNamespace('grddl', XML_GRDDL::NS);

                $xpath     = "//*[@grddl:namespaceTransformation]";
                $attribute = 'namespaceTransformation';

                $xsl = $this->discoverTransformations($namespace, $ns_url,
                                                       $xpath, $attribute,
                                                       XML_GRDDL::NS);

                //Todo: make this stricter to select rdf:Description about:($ns_url)?
                $xpath   = "//grddl:namespaceTransformation";
                $rdf_xsl = $this->discoverTransformations($namespace, $ns_url,
                                                           $xpath, 'resource',
                                                           XML_GRDDL::RDF_NS);

                $transformation_urls = array_merge($xsl, $rdf_xsl);
            }
        } catch (Exception $e) {
            if (empty($this->options['quiet'])) {
                trigger_error($e->getMessage(), E_USER_NOTICE);
            }
        }
        return $transformation_urls;
    }

    /**
     * Inspect a DOMDocument and kludge together a base URI.
     *
     * Otherwise, try to use the existing original document location.
     *
     * @param SimpleXMLElement $sxe          A SimpleXMLElement to inspect
     * @param string           $original_url Where the DOMDocument originally lived
     *
     * @return  string
     */
    protected function determineBaseURI(SimpleXMLElement $sxe, $original_url)
    {

        $bases = $sxe->xpath('//xhtml:head/xhtml:base[@href]');

        if (!empty($bases)) {
            list($base) = $bases;
            return dirname($base['href']) . '/';
        }

        $attributes = $sxe->attributes(XML_GRDDL::XML_NS);
        if (!empty($attributes['base'])) {
            return dirname($attributes['base']) . '/';
        }

        return dirname($original_url) . '/';
    }

    /**
     * Given an XPath[XPATH] root node N with root element E, if the expression
     *
     *  *[local-name()="transformation"
     *      and namespace-uri()=
     *        "http://www.w3.org/2003/g/data-view#"]
     *
     * matches an attribute of an element E, then for each space-separated token
     * REF in the value of that attribute, the resource identified[WEBARCH]
     * by the absolute form (see section 5.2 Relative Resolution in [RFC3986]) of
     * REF with respect to the base IRI[RFC3987],[XMLBASE] of E
     * is a GRDDL transformation of N.
     *
     * Space-separated tokens are the maximal non-empty subsequences not
     * containing the whitespace characters #x9, #xA, #xD or #x20.
     *
     * @param SimpleXMLElement $sxe          Prepopulated document to inspect
     *                                       for transformations.
     * @param string           $original_url The original url this document lived at.
     *
     * @return  string[]    A list of transformations, as urls
     */
    protected function discoverDocumentTransformations(SimpleXMLElement $sxe,
                                                        $original_url = null)
    {
        $xpath = "/*[@grddl:transformation]";
        return $this->discoverTransformations($sxe, $original_url, $xpath,
                                                'transformation', XML_GRDDL::NS);
    }

    /**
     * Transformations can be associated not only with individual documents but
     * also with whole dialects that share an XML namespace.
     * Any resource available for retrieval from a namespace URI is a
     * namespace document (cf. section 4.5.4. Namespace documents in [WEBARCH]).
     * For example, a namespace document may have an XML Schema representation
     * or an RDF Schema representation, or perhaps both, using content negotiation.
     *
     * To associate a GRDDL transformation with a whole dialect, include a
     * grddl:namespaceTransformation property in a GRDDL result of the
     * namespace document.
     *
     * @param SimpleXMLElement $sxe Prepopulated document to inspect for namespaces.
     *
     * @return  string[]    A list of transformations, as urls
     */
    protected function discoverNamespaceTransformations(SimpleXMLElement $sxe)
    {
        //List all namespace urls
        $namespaces = $sxe->getNamespaces(true);

        $transformation_urls = array();


        foreach ($namespaces as $ns_url) {
            //Retrieve or check a local cache for $ns_url
            $urls                = $this->knownNamespaceTransformations($ns_url);
            $transformation_urls = array_merge($transformation_urls, $urls);
        }

        return $transformation_urls;
    }

    /**
     * Inspect a string to see if it is a valid URL
     *
     * @param string $string Input to check
     *
     * @return  bool
     */
    public function isURI($string)
    {
        $url_pattern = '([A-Za-z][A-Za-z0-9+.-]{1,120}:[A-Za-z0-9/](([A-Za-z0-9$_.+!*,;/?:@&~=-])|%[A-Fa-f0-9]{2}){1,333}(#([a-zA-Z0-9][a-zA-Z0-9$_.+!*,;/?:@&~=%-]{0,1000}))?)';
        return (bool)preg_match($url_pattern, $string);
    }

    /**
     * Transform the given XML with the provided XSLT.
     *
     * Driver implementations should override this method.
     *
     * @param string $stylesheet URL or file location of an XSLT transformation
     * @param string $xml        String of XML
     *
     * @return  string  Transformed document contents.
     */
    abstract public function transform($stylesheet, $xml);

    /**
     * Fetch a URL, specifically asking for XML or RDF where available.
     *
     * @param string $path                Path to fetch - typically URL.
     * @param string $preferred_extension Preferred default extension
     *
     * @throws  Exception  Unable to fetch url or file
     *
     * @bug Deal with error response codes to exceptions
     * @bug Deal with ambigious reponse codes (300)
     * @bug Deal with race conditions & url redirection
     *
     * @todo Remove ugly preferred_extension hackery.
     *
     * @return  string  Contents of $path
     */
    public function fetch($path, $preferred_extension = 'html')
    {
        $this->logger->log("Fetching " . $path);

        /** @todo remove me */
        if (empty($path)) {
            throw new Exception("You must provide a path");
        }

        if (isset($this->url_cache[$path])) {
            if ($this->url_cache[$path]['requests']++ > 9) {
                throw new Exception("This resource has been request too many times, possible race condition");
            }

            return $this->url_cache[$path]['data'];
        }

        if ($this->isURI($path)) {
            $req = &new HTTP_Request($path);
            $req->setMethod(HTTP_REQUEST_METHOD_GET);
            $req->addHeader("Accept", 'text/xml, application/xml, application/rdf+xml; q=0.9, */*; text/html q=0.1');
            $req->sendRequest();

            //HTTP 200 OK
            if ($req->getResponseCode() == 200) {
                $this->url_cache[$path] = array();
                $this->url_cache[$path]['requests'] = 1;
                return $this->url_cache[$path]['data'] = $this->prettify($req->getResponseBody());
            }


            // Things which are being Ignored until Later
            //  but Split Out for easy debugging
            // @todo    Does HTTP_Client fix this?
            //HTTP 301 - UH...
            if ($req->getResponseCode() == 301) {
                //For now, return response body, otherwise,
                // consider following redirect?
                $this->url_cache[$path] = array();
                $this->url_cache[$path]['requests'] = 1;
                return $this->url_cache[$path]['data'] = $this->prettify($req->getResponseBody());
            }

            $redirections = array(302, 303, 307);
            if (in_array($req->getResponseCode(), $redirections)) {
                //Obey the Location:
                // @todo ... but consider race conditions
                $headers = $req->getResponseHeader();
                $this->logger->log("The webserver says " . $path . " actually lives at " . $headers['location']);
                return $this->fetch($headers['location']);
            }


            //w3c.org website hacky workarounds
            //ewwwww
            if ($req->getResponseCode() == 300) {
                //further ewww

                $new_path = $this->findRedirect($path);
                if (empty($new_path)) {
                    $new_path = $path . '.' . $preferred_extension;
                }

                return $this->fetch($new_path);
            }



            throw new Exception('HTTP ' . $req->getResponseCode()
                                    . ' while retrieving ' . $path);
        }

        if (file_exists($path) && is_file($path)) {
            $content = file_get_contents($path);

            if ($content) {
                return $this->prettify($content);
            }
        }

        throw new Exception("Unable to fetch " . $path);
    }

    /**
     * Prettify XML.
     *
     * Obeys options for preserveWhiteSpace & formatOutput,
     * and removes redundant namespaces
     *
     * @param string $xml          XML to format
     * @param string $original_url Original document URL
     *
     * @see XML_GRDDL::factory()
     *
     * @return string Formatted XML
     */
    public function prettify($xml, $original_url = null)
    {
        if (empty($xml)) {
            return $xml;
        }

        $dom = new DomDocument('1.0');

        $dom->preserveWhiteSpace = !empty($this->options['preserveWhiteSpace']);
        $dom->formatOutput       = !empty($this->options['formatOutput']);

        $options = LIBXML_NSCLEAN & LIBXML_COMPACT;
        if (!empty($this->options['quiet'])) {
            $options = $options & LIBXML_NOERROR & LIBXML_NOWARNING & LIBXML_ERR_NONE;
            libxml_use_internal_errors(true);
        }

        if ($dom->loadXML($xml, $options)) {

            //
            if (!empty($this->options['xinclude'])) {
                $dom->xinclude($options);
            }

            if (!empty($this->options['quiet'])) {
                libxml_clear_errors();
            }

            return $dom->saveXML($dom);
        } elseif (!empty($this->options['tidy'])) {
            /** @todo   Better way to check if its acutally HTML */
            $config = array(
                       'indent'         => true,
                       'wrap'           => false,
                       'quote-nbsp'     => false,
                       'output-xhtml'   => true);

            $tidy = new tidy();
            $tidy->parseString($xml, $config, 'utf8');
            $tidy->cleanRepair();

            return (string)$tidy;
        }

        return $xml;
    }

    /**
     * Merge two GRDDL results into one.
     *
     * If F and G are GRDDL results of IR, then the merge [RDF-MT] of F and G
     * is also a GRDDL result of IR.
     *
     * ?IR grddl:result ?F, ?G.
     * (?F ?G) log:conjunction ?H.
     *
     *  ?IR grddl:result ?H.
     *
     * @param string $graph_xml1 An RDF/XML graph
     * @param string $graph_xml2 A second RDF/XML graph, to be merged into the first.
     *
     * @bug This method does not check for duplicate nodeIDs
     *
     * @see http://www.w3.org/2004/01/rdxh/spec
     * @see http://www.w3.org/TR/2004/REC-rdf-mt-20040210/#defmerge
     *
     * @return  string  Merged graph containing triples from both original graphs
     */
    public function merge($graph_xml1, $graph_xml2)
    {
        if (empty($graph_xml1)) {
            return $graph_xml2;
        }

        $dom1 = new DomDocument('1.0');
        $dom2 = new DomDocument('1.0');


        $dom1->preserveWhiteSpace = !empty($this->options['preserveWhiteSpace']);
        $dom1->formatOutput       = !empty($this->options['formatOutput']);

        $dom2->preserveWhiteSpace = !empty($this->options['preserveWhiteSpace']);
        $dom2->formatOutput       = !empty($this->options['formatOutput']);

        $dom1->loadXML($graph_xml1, LIBXML_NSCLEAN & LIBXML_COMPACT);
        $dom2->loadXML($graph_xml2, LIBXML_NSCLEAN & LIBXML_COMPACT);

        // pull all child elements of second XML
        $xpath      = new DomXPath($dom2);
        $xpathQuery = $xpath->query('/*/*');

        for ($i = 0; $i < $xpathQuery->length; $i++) {
            // and pump them into first one
            $node = $dom1->importNode($xpathQuery->item($i), true);
            $dom1->documentElement->appendChild($node);
        }

        return $this->prettify($dom1->saveXML());
    }

    /**
     * Fetch, inspect, parse and merge a URL.
     *
     * If you just want to get RDF, and you want to get it now...
     *
     * @param string $url Address of document to crawl.
     *
     * @return  string Resulting RDF document
     */
    public function crawl($url)
    {
        $data        = $this->fetch($url);
        $stylesheets = $this->inspect($data, $url);

        $rdf_xml = array();
        foreach ($stylesheets as $stylesheet) {
            try {
                $rdf_xml[] = $this->transform($stylesheet, $data);
            } catch (Exception $e) {
                $this->logger->log($e->getMessage());
            }
        }

        $result = array_reduce($rdf_xml, array($this, 'merge'));

        return $result;
    }

    /**
     * If this URL has been fetched before,
     * return its resolved location (after all redirects)
     *
     * @param string $url URL
     *
     * @return string
     */
    protected function findRedirect($url)
    {
        return isset($this->url_cache['seeAlso'][$url])? $this->url_cache['seeAlso'][$url] : null;
    }

    /**
     * Record a redirection for a url
     *
     * @param string $url       URL
     * @param string $other_url Redirected URL
     *
     * @return string
     */
    protected function logRedirect($url, $other_url)
    {
        $this->url_cache['seeAlso'][$url] = (string)$other_url;
    }

    /**
     * Append one or more profiles to a HTML document.
     *
     * @param string   $xhtml    Raw XHTML to parse and manipulate
     * @param string[] $profiles Profile urls to insert
     *
     * @return string
     */
    public function appendProfiles($xhtml, $profiles = array())
    {
        if (!is_array($profiles)) {
            $profiles = array();
        }

        return $xhtml;
        /*
        $dom = new DOMDocument('1.0');

        $dom->loadHTML($xhtml);

        $nodes = $dom->documentElement->getElementsByTagName('head');
        $head  = $nodes->item(0);

        if (!$head instanceOf DOMElement) {
            $head = $dom->createElement('head');
            $head = $dom->documentElement->appendChild($head);
        }

        $existing_profiles = explode(' ', $head->getAttribute('profile'));


        $actual_profiles = array_unique(array_merge($profiles, $existing_profiles));

        $head->removeAttribute('profile');
        $head->setAttribute('profile', implode(" ", $actual_profiles));

        return $dom->saveXML();
        */
    }
}
