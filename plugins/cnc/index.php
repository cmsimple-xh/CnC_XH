<?php

/*
 * @version $Id:  $
 *
 */

/**
 * Copyright 2017 Holger Irmler
 *
 * This file is part of Cnc_XH.
 *
 */

//prevent fatal error in older XH-versions
if (!function_exists('XH_afterFinalCleanUp')) {
    return;
}

function cnc_process($html) {
    $command = new Cnc\PrepareOutput;
    $command->execute($html);
}

function hi_cnc() {

    global $plugin_cf;

    if (!isset($_GET['nocache'])
    && $plugin_cf['cnc']['activate']) {
        XH_afterFinalCleanUp('cnc_process');
    }
}

if ($plugin_cf['cnc']['start_auto'] == 'true') {
    hi_cnc();
}