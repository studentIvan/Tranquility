<?php
/**
 * Cron tasks
 */
$__DIR__ = dirname(__FILE__);
require_once $__DIR__ . '/../system/__init__.php';

if (isset($_SERVER['REMOTE_ADDR']) and
    $_SERVER['REMOTE_ADDR'] != '127.0.0.1' and
    $_SERVER['REMOTE_ADDR'] != '::1'
) {
    echo 'access denied';
    exit;
}

try {
    Session::dumpGarbage();
    $config = require $__DIR__ . '/../config/config.php';

    if (isset($config['session']['referrers']) and $config['session']['referrers']) {
        $refd = intval($config['session']['referrers']);
        Database::getInstance()
            ->query("DELETE FROM referrers WHERE timepoint < ( NOW() - INTERVAL $refd DAY )");
    }

    if (isset($config['save_visitors_for']) and $config['save_visitors_for']) {
        $interval = $config['save_visitors_for'];
        Database::getInstance()
            ->query("DELETE FROM visitors WHERE day < DATE( NOW() - INTERVAL $interval )");
    }

    Database::getInstance()
        ->query("TRUNCATE TABLE captcha");

    Database::getInstance()
        ->query("OPTIMIZE TABLE sessions");
} catch (Exception $e) {
}