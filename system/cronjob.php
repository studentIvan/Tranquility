<?php
/**
 * Cron tasks
 */
require_once '__init__.php';

if (isset($_SERVER['REMOTE_ADDR']) and $_SERVER['REMOTE_ADDR'] != '127.0.0.1' and $_SERVER['REMOTE_ADDR'] != '::1')
{
    echo 'access denied';
    exit;
}

Session::dumpGarbage();