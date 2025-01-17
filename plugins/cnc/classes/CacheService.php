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

class CacheService {

    /**
     * @var string
     */
    private $cacheFolder;
    
    /**
     * @var array
     */
    private $lang;

    public function __construct() {
        global $plugin_tx, $pth;

        $this->cacheFolder = "{$pth['folder']['plugins']}cnc/cache/";
        $this->lang = $plugin_tx['cnc'];
    }

    private function cachedFiles() {
        $files = array();
        $files = glob($this->cacheFolder . '*');
        return $files;
    }

    public function deleteFiles() {
        $files = self::cachedFiles();
        $count = count($files);
        $i = 0;
        foreach ($files as $file) {
            if (unlink($file)) {
                $i +=1;
            }
        }
        return $i == $count ? true : false;
    }

    public function cacheInfo() {
        $count = 0;
        $bytes = 0;
        $files = '';
        foreach (new \DirectoryIterator($this->cacheFolder) as $fileInfo) {
            if (!$fileInfo->isDot() && $fileInfo->isFile()) {
                $files .= $fileInfo->getFilename() . '<br>' . "\n";
                $bytes += $fileInfo->getSize();
                $count += 1;
            }
        }
        if ($bytes > 0) {
            $i = floor(log($bytes, 1024));
            $size = round($bytes / pow(1024, $i), [0, 0, 2, 2, 3][$i])
                    . ['B', 'kB', 'MB', 'GB', 'TB'][$i];
        } else {
            $size = $bytes;
        }

        $html = '<div class="cnc_files";>' . "\n";
        $html .= '<p>' . sprintf($this->lang['folder_contains'], $size, $count) . '</p>' . "\n";
        $html .= '<p>' . "\n";
        $html .= $files;
        $html .= '</p>' . "\n";
        $html .= '</div>' . "\n";
        return $html;
    }

}
