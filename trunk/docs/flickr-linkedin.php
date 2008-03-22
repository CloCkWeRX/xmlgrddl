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
 * @version   SVN: $Id: an-hcard-profile.php 28 2008-03-01 09:13:15Z daniel.oconnor $
 * @link      http://code.google.com/p/xmlgrddl/
 */

require_once 'XML/GRDDL.php';

/**
 * Example: Fetch multiple URLs about a specific user
 * and get useful information out.
 */
$urls = array();
$urls[0] = 'http://flickr.com/people/clockwerx/';
$urls[1] = 'http://www.linkedin.com/in/clockwerx';
$urls[2] = 'http://www.last.fm/user/CloCkWeRX/';
$urls[3] = 'http://clockwerx.blogspot.com/';

//For each URL, pretend it has these urls in <head profile="foo" />
//These look for hcard, hcalendar, etc.
$profiles[$urls[0]][] = 'http://www.w3.org/2002/12/cal/cardcaletc';
$profiles[$urls[1]][] = 'http://microformats.org/wiki/hresume-profile';
$profiles[$urls[1]][] = 'http://www.w3.org/2002/12/cal/cardcaletc';
$profiles[$urls[2]][] = 'http://www.w3.org/2002/12/cal/cardcaletc';
$profiles[$urls[3]][] = 'http://www.w3.org/2002/12/cal/cardcaletc';

//Set what kind of transformations we're interested in.


$options = XML_GRDDL::getDefaultOptions();
$options['quiet'] = true;

$grddl = XML_GRDDL::factory('xsl', $options);
$results = array();
foreach ($urls as $n => $url) {
    $data = $grddl->fetch($url);

    $data = $grddl->prettify($data);

    $modified_data = $grddl->appendProfiles($data, $profiles[$url]);

    $stylesheets = $grddl->inspect($modified_data, $url);

    $rdf_xml = array();
    foreach ($stylesheets as $stylesheet) {
        $rdf_xml[] = $grddl->transform($stylesheet, $modified_data);
    }

    $results[$url] = array_reduce($rdf_xml, array($grddl, 'merge'));
}

print "We scuttered " . count($urls) . " urls and found these results\n";
foreach ($results as $url => $rdf_xml) {
    print $url . "\n";
    print $rdf_xml . "\n\n";
}