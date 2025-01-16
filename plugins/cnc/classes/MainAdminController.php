<?php

/**
 * 
 * @version $Id:  $
 * 
 * Copyright 2017 Christoph M. Becker
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

class MainAdminController
{
    /**
     * @var string
     */
    private $scriptName;

    /**
     * @var array
     */
    private $lang;

    /**
     * @var object
     */
    private $csrfProtector;

    public function __construct()
    {
        global $sn, $plugin_tx, $title, $_XH_csrfProtection;

        $this->scriptName = $sn;
        $this->lang = $plugin_tx['cnc'];
        $this->csrfProtector = $_XH_csrfProtection;
        $title = XH_hsc($this->lang['menu_main']);
    }

    public function defaultAction()
    {
        $view = new View('main');
        $view->url = "{$this->scriptName}?&cnc&edit";
        $view->admin = 'plugin_main';
        $view->csrfToken = new HtmlString($this->csrfProtector->tokenInput());
        $cache = new CacheService;
        $view->info = new HtmlString($cache->cacheInfo());
        $view->render();
    }

    public function deleteAction()
    {
        $this->csrfProtector->check();
        $cache = new CacheService;
        if ($cache->deleteFiles()) {
            header('Location: ' . CMSIMPLE_URL . '?&cnc&admin=plugin_main&action=deleted&normal', true, 303);
            exit;
        } else {
            echo XH_message('fail', $this->lang['message_export_failed']);
        }
    }

    public function deletedAction()
    {
        echo XH_message('success', $this->lang['message_deleted']);
        echo XH_message('info', $this->lang['message_rebuild']);
    }
}