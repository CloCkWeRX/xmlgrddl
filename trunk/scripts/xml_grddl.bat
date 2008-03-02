@echo off

REM XML_GRDDL
REM 
REM Copyright (c) 2008, Daniel O'Connor <daniel.oconnor@gmail.com>.
REM All rights reserved.
REM 
REM Redistribution and use in source and binary forms, with or without
REM modification, are permitted provided that the following conditions
REM are met:
REM 
REM  * Redistributions of source code must retain the above copyright
REM     notice, this list of conditions and the following disclaimer.
REM 
REM  * Redistributions in binary form must reproduce the above copyright
REM     notice, this list of conditions and the following disclaimer in
REM     the documentation and/or other materials provided with the
REM     distribution.
REM 
REM  * Neither the name of Daniel O'Connor nor the names of his
REM     contributors may be used to endorse or promote products derived
REM     from this software without specific prior written permission.
REM 
REM THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
REM "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
REM LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
REM FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
REM COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
REM INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
REM BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
REM LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
REM CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
REM LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
REM ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
REM POSSIBILITY OF SUCH DAMAGE.
REM 
REM @category  Semantic_Web
REM @package   XML_GRDDL
REM @author    Daniel O'Connor <daniel.oconnor@gmail.com>
REM @copyright 2008 Daniel O'Connor
REM @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
REM @version   SVN: $Id: example.php 52 2008-03-01 14:00:43Z daniel.oconnor $
REM @version   @package_version@
REM @link      http://code.google.com/p/xmlgrddl/


set PHPBIN="G:\php\.\php.exe"
REM "@php_bin" "process-grddl.php" %*
"G:\php\.\php.exe" -d safe_mode=Off "process-grddl.php" %*

REM     <tasks:replace from="@php_bin@" to="php_bin" type="pear-config" />
REM     <tasks:replace from="@php_dir@" to="php_dir" type="pear-config" />
