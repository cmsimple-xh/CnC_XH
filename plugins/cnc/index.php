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

//if (!XH_ADM && !isset($_GET['nocache']) && $plugin_cf['cnc']['activate'] == 'true') {
if (!isset($_GET['nocache']) && $plugin_cf['cnc']['activate']) {
    XH_afterFinalCleanUp('cnc_process');
}