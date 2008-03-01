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

require_once 'XML/GRDDL.php';
require_once 'PHPUnit/Framework/Assert.php';

//See http://www.w3.org/TR/grddl-tests/#grddl-library
$tests = array();

//Localized Tests
//
$tests[] = array('name' => 'P3P work-alike',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/xmlWithGrddlAttribute.xml',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/xmlWithGrddlAttribute-output.rdf');

$tests[] = array('name' => 'Get RDF from a spreadsheet',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/projects.xml',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/projects.rdf');

$tests[] = array('name' => 'RDFa example',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/rdf_sem.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/rdf_sem-output.rdf');

$tests[] = array('name' => 'Inline transformation reference',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/inline.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/inline.rdf');

$tests[] = array('name' => 'Base URI: Same document reference',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/baseURI.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/baseURI.rdf');

$tests[] = array('name' => 'Title / Author (from specification)',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/titleauthor.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/titleauthor-output.rdf');

/* //Skipping test: We'll never try to transform a document with no transformations
$tests[] = array('name' => 'RDF/XML document',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/projects.rdf',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/projects.rdf');
*/

$tests[] = array('name' => 'One transform linked from the head of a document with only the GRDDL profile',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/xhtmlWithGrddlProfile.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/xhtmlWithGrddlProfile-output.rdf');


$tests[] = array('name' => 'One transform linked from the body of a document with only the GRDDL profile',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/xhtmlWithGrddlTransformationInBody.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/xhtmlWithGrddlTransformationInBody-output.rdf');


$tests[] = array('name' => 'One transform linked from the head of a document with several profiles, including the GRDDL profile',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/xhtmlWithMoreThanOneProfile.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/xhtmlWithTwoTransformations-output.rdf');


$tests[] = array('name' => 'Two transformations linked from the body of a document with the GRDDL profile',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/xhtmlWithMoreThanOneGrddlTransformation.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/xhtmlWithTwoTransformations-output.rdf');


$tests[] = array('name' => 'XML document linking to its transformer through the GRDDL attribute',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/xmlWithGrddlAttributeAndNonXMLNamespaceDocument.xml',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/xmlWithGrddlAttributeAndNonXMLNamespaceDocument-output.rdf');

//Namespace Documents and Absolute Locations
$tests[] = array('name' => 'An hcard profile',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/card.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/card-output.rdf');

$tests[] = array('name' => '2 profiles: eRDF and hCard',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/multiprofile.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/multiprofile-output.rdf');

$tests[] = array('name' => 'Namespace documents and media types 1',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/sq1.xml',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/sq1-output.rdf');

$tests[] = array('name' => 'Namespace documents and media types 2',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/sq2.xml',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/sq2-output.rdf');

$tests[] = array('name' => 'A variant of the card5n test',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/card5na.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/card5n-output.rdf');

$tests[] = array('name' => 'hcard from a 1998 review comment on P3P',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/card5n.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/card5n-output.rdf');

$tests[] = array('name' => 'A copy of the hcard profile',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/hcard.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/hcard-output.rdf');
/** @bug issue 8 */
/*
$tests[] = array('name' => 'An XML document with two namespace transformations',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/two-transforms.xml',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/two-transforms-output.rdf');
*/
$tests[] = array('name' => 'An XML document with two namespace transformations and a transform on the root element',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/three-transforms.xml',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/three-transforms-output.rdf');

$tests[] = array('name' => 'An XML document with two namespace transformations and two transforms on the root element',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/four-transforms.xml',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/four-transforms-output.rdf');

$tests[] = array('name' => 'A variant of the hcard profile',
                 'in' => 'http://www.w3.org/2001/sw/grddl-wg/td/hcarda.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/hcard-output.rdf');


$tests[] = array('name' => 'Document linking to its transformer through a GRDDL-enabled profile',
                 'in'  => 'http://www.w3.org/2001/sw/grddl-wg/td/xhtmlWithGrddlEnabledProfile.html',
                 'out' => 'http://www.w3.org/2001/sw/grddl-wg/td/xhtmlWithGrddlEnabledProfile-output.rdf');

// Library tests
/*
    *  Embedded RDF1
      input output
      xml

      a simple test for embedded RDF.

      Approval: 2007-06-27
    * Embedded RDF2
      input output
      xml

      a test for embedded RDF, with two blocks of RDF

      Approval: 2007-06-27
    * Embedded RDF3
      input output
      xml,rdfx-base

      a test for embedded RDF. A corner case: an RDF document.

      Approval: 2007-06-27
    * Glean Profile
      input output
      grddl-profile

      a test for glean profile, checking the treatment of spaces in the rel attribute.

      Approval: 2007-04-25
    * Embedded RDF using a relative xml:base:
      input output
      xml

      Approval: 2007-06-27
    * Embedded RDF using an absolute xml:base:
      input output
      xml

      Approval: 2007-06-27
    * Embedded RDF using two nested absolute xml:base:
      input output
      xml

      Approval: 2007-06-27
    * Embedded RDF using two different xml:base on two different blocks of RDF:
      input output
      xml

      Approval: 2007-04-25
    * Embedded RDF using two different xml:lang on two different blocks of RDF:
      input output
      xml

      Approval: 2007-06-27
    * Embedded RDF using two different inherited xml:lang on two different blocks of RDF:
      input output
      xml

      Approval: 2007-06-27
    * An XHTML profile using a base element
      input output
      grddl-profile

      This is from the final URI.

      Approval: 2007-04-25
    * An XHTML profile using a base element
      input output
      grddl-profile

      This is from a redirected URI.

      Approval: 2007-04-25
    * XHTML with an XHTML profile using a base element
      input output
      grddl-profile,other-profile

      This shows intended use of the profile.

      Approval: 2007-04-25

*/

/*

Ambiguous Infosets, Representations, and Traversals

These tests help check for robustness of implementations in the face of various odd cases.

    * Namepace Loop
      input output
      xml,merge,ns

      Approval: 2007-04-25
    * Testing GRDDL when XInclude processing is enabled
      input output
      xml,merge

      In this test case, the input file uses XInclude to reference xinclude2.xml, and the output has only one triple unless the XML Processor of the GRDDL implementation supports XInclude. The output for this case assumes that the processor does resolve XIncludes. This test case (and the one that follows) exercises the Working Group's resolution regarding faithful infosets. In particular, the output illustrates a situation where the XML processor employed invokes XInclude processing at a low-level and presents the expanded result infoset [XINCLUDE] to the higher-level application (the GRDDL-aware agent. See: 1.1 Relationship to XLink [XINCLUDE]).
      XInclusion and GRDDL

      Approval: 2007-04-11

      This pair of tests anticipate that the resolution of TAG issue xmlFunctions-34 will provide further guidance concerning them.
    * Testing GRDDL when XInclude processing is disabled
      input output
      xml,merge

      This test case is an alternative to the XInclude enabled test case. The output for this case assumes that the processor does not resolve XIncludes, which may lead to a different GRDDL result. Note that the unexpanded infoset and its corresponding XPath Data Model (See: B XML Information Set Mapping [XPATH]) could instead have been presented to an XProc pipeline with an explicit XInclude component.

      Approval: 2007-04-11
    * Testing GRDDL attributes on RDF documents
      input output
      xml,merge,rdfx-base

      Note that the input is an RDF document with a GRDDL transformation, and that according to the rules given by the GRDDL Specification, there are three distinct and equally valid output graphs for this test for this document. This output is a graph that is merge of the graph given by the source document with the graph given by the result of the GRDDL transformation.

      Approval: 2007-04-25
    * Spaces in rel attribute
      input output
      grddl-profile

      The rel attribute can take multiple values.

      Approval: 2007-04-25
    * Recursion 1
      input output
      merge,ns,rdfx-base,other-profile

      The layering tests, permit arbitrary nesting (up to depth 9) of HTML profiles and XML namespaces. The general pattern is:
          o Take a string $V matching ((ns|pf)-){0-8}.
          o The document ns-$Vfnd is an xml document with namespace $Vfnd.
          o The document pf-$Vfnd is an xhtml document with profile $Vfnd.
          o The RDF/XML document fnd specifies appropriate transformations, so that every possible stack have GRDDL results. These are all different.
          o The output document fnd-$Voutput.srdf is the correct answer.

      An HTML document which has a profile being an HTML document, which has a profile being an HTML document, which has a profile being an XML document, which has an RDF namespace document.

      Approval: 2007-04-18
    * Recursion 2
      input output
      merge,ns,rdfx-base,other-profile

      An XML document which has an XML namespace document, which has an HTML namespace document, which has a profile being an HTML document, which has a profile being an RDF document./

      Approval: 2007-04-18
    * Recursion 3
      input output
      merge,ns,rdfx-base,other-profile

      An XML document which has an HTML namespace document, which has a profile being an XML document, which has an HTML namespace document, which has a profile being an XML document, which has an RDF namespace document.

      Approval: 2007-04-18
    * Namespace loop
      input output
      xml,merge,ns

      The following four tests demonstrate GRDDL results for a self-referencing input document. Unlike other tests of this kind, the last of these - the maximal result - is not exlusive. This reflects an interpretation of SHOULD as used in section 7. GRDDL-Aware Agents of [GRDDL] with regards to the computation of GRDDL results. In particular, this interpretation and the text in the section that follows (8. Security considerations) permits an implementation to only pass the first test due to security restrictions against computing recursive GRDDL results.

      For this particular test, an XML document is its own namespace document, with a GRDDL transformation, specifying a namespaceTransformation, which specifies a further namespaceTransformation. This result is the first possible GRDDL result. Implementations that make no allowance for such cases may produce this result. Documents authors are advised against having information resources whose GRDDL results depend on other GRDDL results for the same resource.

      Approval: 2007-04-25
    * Namespace loop
      input output
      xml,merge,ns

      An XML document is its own namespace document, with grddl transformation, specifying a namespaceTransformation, which specifies a further namespaceTransformation. This result is the merge of the first two possible GRDDL results. Implementations that make no special allowance for or prohibition of such cases may produce this result. Documents authors are advised against having information resources whose GRDDL results depend on other GRDDL results for the same resource.

      Approval: 2007-04-25
    * Namespace loop
      input output
      xml,merge,ns

      An XML document is its own namespace document, with grddl transformation, specifying a namespaceTransformation, which specifies a further namespaceTransformation. This result is the merge of the first three possible GRDDL results. Implementations that make no special allowance for or prohibition of such cases may produce this result. Documents authors are advised against having information resources whose GRDDL results depend on other GRDDL results for the same resource.

      Approval: 2007-04-25
    * Namespace loop
      input output
      xml,ns,rdfx-base

      An XML document is its own namespace document, with a GRDDL transformation, specifying a namespaceTransformation, which specifies a further namespaceTransformation. This result is the merge of all possible GRDDL results. Documents authors are advised against having information resources whose GRDDL results depend on other GRDDL results for the same resource.

      Approval: 2007-04-25
    * HTML document with transformation attribute on root
      input, output.
      xml,grddl-profile

      Two transforms apply to this document, following rules in both sections 2 and 4 of the specification.

      Approval: 2007-04-25
    * Document linking to its transformer through a GRDDL-enabled profile, and with in-line transformation
      input output
      merge,grddl-profile,other-profile

      An XHTML file with a profile whose interpretation through GRDDL gives a transformation for the said XHTML file; the document also specifies the GRDDL profile, and a transformation.

      Approval: 2007-04-25
    * Document linking to its transformer through a GRDDL-enabled profile, and with in-line transformation
      input output
      grddl-profile,other-profile

      An XHTML file with a profile whose interpretation through GRDDL gives a transformation for the said XHTML file; the document also specifies a transformation, but omits to specify the GRDDL profile.

      Approval: 2007-04-25
    * Testing GRDDL attributes on RDF documents with XML media type
      input output
      xml,merge,rdfx-base

      This test differs from the previous example of applying GRDDL to an RDF/XML document in that the RDF file is served (not best practice, but rather common) as media-type "application/xml". The output is a graph that is merge of the graph given by the source document with the graph given by the result of the GRDDL transformation.

      Approval: 2007-04-18
    * Content Negotiation with GRDDL (1 of 2)
      input output
      xml

      This test exists to bring attention to developers to issues of content negotiation, in particular, content negotiation over language as described and implemented by W3C QA. There are two valid resulting GRDDL results of running this GRDDL transformation depending on what language the GRDDL-aware agent uses, and an implementation of a GRDDL-aware agent only needs to retrieve the one that is appropriate for its HTTP header request. This result follows from retrieving a English version of the HTML representation and thus having the GRDDL result produce a result with English-language content.

      Approval: 2007-04-25
    * Content Negotiation with GRDDL (2 of 2)
      input output
      xml

      This result follows from retrieving a German version of the HTML representation and thus having the GRDDL result produce a result with German-language content.

      Approval: 2007-04-25
    * Content Negotation with GRDDL (3 of 3):
      input output
      xml,merge

      A GRDDL aware agent may retrieve both representations, for example, by using transparent content negotiation. This GRDDL result is the merge of the previous two.

      Approval: 2007-04-25
    * Multiple Representations (HTML):
      input output
      grddl-profile

      This test gives the GRDDL result of the HTML representation.

      Approval: 2007-04-25
    * Multiple Representations (SVG):
      input output
      xml

      This test gives the GRDDL result of the SVG representation.

      Approval: 2007-04-25
    * Multiple Representations (both):
      input output
      xml,merge,grddl-profile

      This GRDDL result is the merge of the previous two.

      Approval: 2007-04-25
    * An html document with a base element:
      input output
      grddl-profile

      Approval: 2007-04-25
    * A similar html document without a base element:
      input output
      grddl-profile

      Approval: 2007-04-25
    * A redirected html document with a base element:
      input output
      grddl-profile

      Approval: 2007-04-25
    * A similar redirected html document without a base element:
      input output
      grddl-profile

      Approval: 2007-04-25
    * An xml document with an xml:base attribute:
      input output
      xml

      This test case exercises resolution of relative references found in the GRDDL results for a general XML document. In this case, according to RFC 3986, section 5.1, a base URI for the relative reference is recursively discovered on the encapsulating entity for the GRDDL results, which is the root element of the input document, in order to maintain fidelity to the faithful rendition requirement. The root element assigns the base URI using the mechanism described in XML Base.

      Approval: 2007-04-27
    * A similar xml document without an xml:base attribute:
      input output
      xml

      This test case exercises resolution of relative references found in the GRDDL results for a general XML document. In this case, according to RFC 3986, section 5.1, a base URI for the relative reference is recursively discovered to be the URI used to retrieve the input document, since no base URI is assigned in the content of the encapsulating entity (that is, the root element of the input document).

      Approval: 2007-04-27
    * A redirected xml document with an xml:base attribute:
      input output
      xml

      This test case exercises resolution of relative references found in the GRDDL results for a general XML document when that document is resolved through a protocol redirection mechanism. The base URI for these relative references is established by the xml:base attribute on the root element, as for "An xml document with an xml:base attribute".

      Approval: 2007-04-27
    * A similar redirected xml document without an xml:base attribute:
      input output
      xml

      This test case exercises resolution of relative references found in the GRDDL results for a general XML document when that document is resolved through a protocol redirection mechanism. The base URI of the document is the target URI of the last redirection step; after establishing this fact, this test case follows the same behavior as "A similar xml document without an xml:base attribute".

      Approval: 2007-04-27

*/
foreach ($tests as $test) {
    $options = array('documentTransformations' => true,
                        'namespaceTransformations'   => true,
                        'htmlTransformations'     => true,
                        'htmlProfileTransformations' => true);

    $grddl = XML_GRDDL::factory('xsl', $options);

    $in  = $grddl->fetch($test['in']);
    $out = $grddl->fetch($test['out']);

    $stylesheets = $grddl->inspect($in, $test['in']);

    $rdf_xml = array();
    foreach ($stylesheets as $stylesheet) {
        $rdf_xml[] = $grddl->transform($stylesheet, $in);
    }

    $result = array_reduce($rdf_xml, array($grddl, 'merge'));

    try {
        PHPUnit_Framework_Assert::assertSame($out, $result);
    } catch (PHPUnit_Framework_AssertionFailedError $e) {
        print $test['name'] . "\n";
        print $e->toString() . "\n\n";
        print "Got:\n";
        print $result . "\n";
        print "Expected:\n";
        print $out . "\n\n";
    }

}

/*
$options = array('documentTransformations' => true, 'namespaceTransformations' => true);
$grddl = XML_GRDDL::factory('xsl', $options);

//$xml = $grddl->fetch(dirname(__FILE__) . '/tests/XML_GRDDL/data/test_02.xml');
$url = 'http://www.w3.org/2001/sw/grddl-wg/td/titleauthor.html';
$xml = $grddl->fetch($url);

$stylesheets = $grddl->inspect($xml, $url); //Returns a list of available XSL transformations

$rdf_xml = array();
foreach ($stylesheets as $stylesheet) {
    $rdf_xml[] = $grddl->transform($stylesheet, $xml);
}



print_r($stylesheets);
print_r($rdf_xml);
*/