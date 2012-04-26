<?php
/**
 * Standard solution
 */
$__DIR__ = dirname(__FILE__);

if (isset($config['cms']))
{
    if (isset($config['cms']['news']) and $config['cms']['news']) {
        require_once $__DIR__ . '/models/News.php';
    }

    if (isset($config['cms']['users']) and $config['cms']['users']) {
        require_once $__DIR__ . '/models/Users.php';
        require_once $__DIR__ . '/models/Roles.php';
    }
}