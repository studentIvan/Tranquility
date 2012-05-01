<?php
/**
 * Standard solution
 */
$__DIRADM__ = dirname(__FILE__);

if (isset($config['cms']))
{
    if (isset($config['cms']['news']) and $config['cms']['news']) {
        require_once $__DIRADM__ . '/datamappers/News.php';
    }

    if (isset($config['cms']['users']) and $config['cms']['users']) {
        require_once $__DIRADM__ . '/datamappers/Users.php';
        require_once $__DIRADM__ . '/datamappers/Roles.php';
    }
}