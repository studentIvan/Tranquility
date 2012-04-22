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

if (isset($config['sessions']['referers']) and $config['sessions']['referers'])
{
    $refd = intval($config['sessions']['referers']);
    Database::getInstance()
        ->query("DELETE FROM referers WHERE timepoint < ( NOW() - INTERVAL $refd DAY )");
}