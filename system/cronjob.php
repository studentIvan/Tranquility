<?php
/**
 * Cron tasks
 */
require_once '__init__.php';

if (isset($_SERVER['REMOTE_ADDR']) and
    $_SERVER['REMOTE_ADDR'] != '127.0.0.1' and
        $_SERVER['REMOTE_ADDR'] != '::1')
{
    echo 'access denied';
    exit;
}

try {
    Session::dumpGarbage();
    $config = require dirname(__FILE__) . '/../config/config.php';

    if (isset($config['session']['referrers']) and $config['session']['referrers']) {
        $refd = intval($config['session']['referrers']);
        Database::getInstance()
            ->query("DELETE FROM referrers WHERE timepoint < ( NOW() - INTERVAL $refd DAY )");
    }
}
catch (Exception $e) {
}

try {
    Database::getInstance()
        ->query("TRUNCATE TABLE captcha");
}
catch (Exception $e) {
}

try {
    Database::getInstance()
        ->query("OPTIMIZE TABLE sessions");
}
catch (Exception $e) {
}