<?php
/**
 * Turbo Batman CMF
 *
 */
define('STARTED_AT', microtime(true));

$config = require 'config.php';

define('DEVELOPER_MODE', isset($config['developer_mode']) ? $config['developer_mode'] : false);

require_once __DIR__ . '/vendor/Twig/Autoloader.php';

Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem(__DIR__ . '/views');
$twig = new Twig_Environment($loader, array(
    'cache' => DEVELOPER_MODE ? false : __DIR__ . '/system/cache',
));

$output = array();

$output['mobile'] =
    (isset($config['always_mobile']) and $config['always_mobile']) ?
    true : require __DIR__ . '/system/ismobile.php';

$output['resource'] = isset($config['resources']) ? $config['resources'] : array();

echo $twig->render('homepage.html.twig', $output);

if (DEVELOPER_MODE)
    echo "\n<!-- PAGE EXECUTION TIME: ", number_format((microtime(true) - STARTED_AT), 4), " -->";