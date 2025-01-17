<?php

/**
 * 
 * @version $Id:  $
 * 
 * Copyright 2017 Holger Irmler
 *
 * This file is part of Cnc_XH.
 *
 * Cnc_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Cnc_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Cnc_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Cnc;

use DomDocument;

class PrepareOutput {

    /**
     * @var string
     */
    private $pluginFolder;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    private $minUrl;

    /**
     * @var array
     */
    private $ignoreJs;

    /**
     * @var array
     */
    private $ignoreCss;
    
    /**
     * @var bool
     */
    private $useGzip;

    public function __construct() {
        global $cf, $plugin_cf, $pth, $sl;

        $this->pluginFolder = "{$pth['folder']['plugins']}cnc/";
        $this->prefix = $sl === $cf['language']['default'] ? './' : '../';
        $this->minUrl = $this->prefix . 'min/index.php?f=';
        $this->ignoreJs = array_map('trim',
                explode(',', $plugin_cf['cnc']['ignore_js']));
        $this->ignoreCss = array_map('trim',
                explode(',', $plugin_cf['cnc']['ignore_css']));
        $this->useGzip = $plugin_cf['cnc']['compress_html'];
    }

    private function inIgnoreList($haystack, $needle, $offset = 0) {
        if (!is_array($needle)) {
            $needle = array($needle);
        }
        foreach ($needle as $query) {
            if ($query != '') {
                if (strpos($haystack, $query, $offset) !== false) {
                    return true; // stop on first true result
                }
            }
        }
        return false;
    }

    private function isLocalJavascript($src) {
        //grab only local referenced scripts, no CDNs...
        if ($src != '' && strpos($src, $this->prefix) !== false) {
            return true;
        }
        return false;
    }

    private function isLocalStylesheet($href) {
        //again only local stylesheets
        if ($href != '' && strpos($href, $this->prefix) !== false) {
            return true;
        }
        return false;
    }

    public function execute($html) {
        $dom = new DOMDocument();
        $oldErrors = libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
        libxml_use_internal_errors($oldErrors);

        $scripts = [];
        //find all script-tags
        $scripts = $dom->getElementsByTagName('script');
        //rewrite JS-URLs
        foreach ($scripts as $script) {
            $src = $script->getAttribute('src');
            //grab only local referenced scripts, no CDNs...
            if ($this->isLocalJavascript($src) 
                    && !$this->inIgnoreList($src, $this->ignoreJs)) {
                $script->setAttribute('src',
                        $this->minUrl
                        . str_replace('../', './', $src)
                        . '&' . filemtime($src));
            }
        }
        //rewrite CSS-URLs, but search only for childs of <head>
        $elements = $dom->getElementsByTagName('head');
        foreach ($elements as $node) {
            foreach ($node->childNodes as $child) {
                if ($child->nodeName == 'link' 
                        && $child->getAttribute('rel') == 'stylesheet') {
                    $href = $child->getAttribute('href');
                    if ($this->isLocalStylesheet($href) 
                            && !$this->inIgnoreList($href, $this->ignoreCss)) {
                        $child->setAttribute('href',
                                $this->minUrl
                                . str_replace('../', './', $href)
                                . '&' . filemtime($href));
                    }
                }
            }
        }
        $dom->formatOutput = true;
        $output = $dom->saveHTML();

        $compress = (isset($_SERVER['HTTP_ACCEPT_ENCODING'])
                    && substr_count(strtolower($_SERVER['HTTP_ACCEPT_ENCODING']), 'gzip')
                    && function_exists('gzencode')
                    && $this->useGzip
                        ? true
                        : false);

        if ($compress) {
            $output = gzencode($output);
            header('Content-Encoding: gzip');
        }

        //Implement cache for dynamic generated page
        //only for $_GET requests; don't cache if a Session is running
        if ($_SERVER['REQUEST_METHOD'] == 'GET') 
        //if ($_SERVER['REQUEST_METHOD'] == 'GET' && !isset($_SESSION)) 
        {
            $cl_etag = !empty($_SERVER['HTTP_IF_NONE_MATCH']) 
                    ? trim($_SERVER['HTTP_IF_NONE_MATCH'])
                    : null;

            $cl_accept_encoding = isset($_SERVER['HTTP_ACCEPT_ENCODING']) 
                    ? $_SERVER['HTTP_ACCEPT_ENCODING']
                    : '';

            $https = isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : '';

            //calculate a unipue string as ETag
            $sv_etag = md5($output . $cl_accept_encoding . $https);

            //does client and server ETags match?
            //We can't trust the client to always enclose the ETag in "normal"
            //quotation marks, so we use strpos to detect a match
            $matching_etag = $cl_etag && strpos($cl_etag, $sv_etag) !== false;

            //If so, exit here with a 304 and send no further data
            //so the client will use the already cached version
            if ($matching_etag) {
                header($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified');
                exit(304);
            }

            //Otherwise, send the page witch correct headers
            //(always revalidate ETag)
            header('Cache-Control: no-cache, must-revalidate', true);
            // set new ETag header for cache recognition
            header('ETag: "' . $sv_etag . '"');
        }
        
        //we can simply echo our html here...
        if ($compress) {
            //header('Content-Encoding: gzip');
        }
        echo $output;
    }

}
