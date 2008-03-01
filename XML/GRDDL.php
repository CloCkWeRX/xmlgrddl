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
 * @version   SVN: $Id: AssertionFailedError.php 1985 2007-12-26 18:11:55Z sb $
 * @link      http://code.google.com/p/xmlgrddl/
 */

class XML_GRDDL
{
    const NS       = "http://www.w3.org/2003/g/data-view#";
    const XHTML_NS = 'http://www.w3.org/1999/xhtml';

    /**
     * Instantiate a new instance of a GRDDL driver.
     *
     * @param string  $driver  Name of driver. Default is 'xsl'.
     * @param mixed[] $options An array of options, refer to individual drivers
     *                         document for more information.
     *
     * @return  XML_GRDDL_Driver
     */
    public static function factory($driver = 'Xsl',
                                   $options = array('documentTransformations' => true,
                                                    'htmlTransformations' => true,
                                                    'htmlProfileTransformations' => true,
                                                    'namespaceTransformations' => true))
    {
        $class = 'XML_GRDDL_Driver_' . $driver;

        $path = dirname(__FILE__) . '/GRDDL/Driver/' . $driver . '.php';

        if (file_exists($path)) {
            include_once $path;
        }

        if (!class_exists($class)) {
            throw new Exception("Unknown driver " . $class);
        }

        return new $class($options);
    }
}