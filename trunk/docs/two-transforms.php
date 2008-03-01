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
 *   * Neither the name of Sebastian Bergmann nor the names of his
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
 * @version   SVN: $Id: namespace-documents2.php 23 2008-03-01 04:00:26Z daniel.oconnor $
 * @link      http://code.google.com/p/xmlgrddl/
 */

require_once 'XML/GRDDL.php';

/**
 * An XML document with two namespace transformations
 *
 * Compare the results to http://www.w3.org/2001/sw/grddl-wg/td/two-transforms-output.rdf
 */

$url = 'http://www.w3.org/2001/sw/grddl-wg/td/two-transforms.xml';

//Set what kind of transformations we're interested in.
$options = array('documentTransformations' => true,     // XML
                 'namespaceTransformations' => true,    // XML namespaces
                 'htmlTransformations' => true,         // HTML <link> transforms
                 'htmlProfileTransformations' => true); // HTML Profile transform

$grddl = XML_GRDDL::factory('xsl', $options);

$data        = $grddl->fetch($url);
$stylesheets = $grddl->inspect($data, $url);

var_dump($data);
var_dump($stylesheets);
$rdf_xml = array();
foreach ($stylesheets as $stylesheet) {
    $rdf_xml[] = $grddl->transform($stylesheet, $data);
}

$result = array_reduce($rdf_xml, array($grddl, 'merge'));

var_dump($result);
//var_dump($grddl->crawl($url));
