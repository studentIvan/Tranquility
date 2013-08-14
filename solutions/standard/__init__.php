<?php
/**
 * Standard solution
 */
$__DIRADM__ = dirname(__FILE__);
Process::$context['debug'] = DEVELOPER_MODE;

$sCfg = Session::getOptions();
Process::$context['session_lifetime_hours'] = $sCfg['lifetime_hours'];
Process::$context['session_garbage_auto_dump'] = $sCfg['garbage_auto_dump'];
Process::$context['hosting_free_space_mb'] =
    isset($config['hosting_free_space_mb']) ? $config['hosting_free_space_mb'] : 1024;

if (isset($config['cms'])) {
    if (isset($config['cms']['news']) and $config['cms']['news']) {
        require_once $__DIRADM__ . '/datamappers/News.php';
    }

    if (isset($config['cms']['users']) and $config['cms']['users']) {
        require_once $__DIRADM__ . '/datamappers/Users.php';
        require_once $__DIRADM__ . '/datamappers/Roles.php';
    }

    require_once $__DIRADM__ . '/datamappers/Stats.php';

    if (isset($_SERVER['REMOTE_ADDR']))
        Stats::registerVisit();

    $config['cms']['admin_cfg'] = require_once $__DIRADM__ . '/../../config/admin.config.php';
}