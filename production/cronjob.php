<?php
/**
 * Cron tasks
 */
$__DIR__ = dirname(__FILE__);
require_once $__DIR__ . '/../system/__init__.php';

try {
    Session::dumpGarbage();
    $base = json_decode(file_get_contents($__DIR__ . '/../config/base.json'), true);
    $additional = json_decode(file_get_contents($__DIR__ . '/../config/dynamical.json'), true);
    $config = array_merge($base, $additional);
} catch (Exception $e) {
    echo $e->getMessage();
}

try {
    if (isset($config['session']['referrers']) and $config['session']['referrers']) {
        $interval = $config['session']['referrers'];
        Database::getInstance()
            ->query("DELETE FROM referrers WHERE timepoint < ( NOW() - INTERVAL $interval )");
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

try {
    if (isset($config['save_visitors_for']) and $config['save_visitors_for']) {
        $interval = $config['save_visitors_for'];
        Database::getInstance()
            ->query("DELETE FROM visitors WHERE day < DATE( NOW() - INTERVAL $interval )");
    }
} catch (Exception $e) {
    echo $e->getMessage();
}

try {
    Database::getInstance()
        ->query("TRUNCATE TABLE captcha");
} catch (Exception $e) {
    echo $e->getMessage();
}

try {
    Database::getInstance()
        ->query("OPTIMIZE TABLE sessions");
} catch (Exception $e) {
    echo $e->getMessage();
}
