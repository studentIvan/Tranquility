<?php
/**
 * Routes configuration
 *
 * string rule => string|array routes
 * if route contain .php - this is controller
 */
return array(
    '/' => 'homepage',
    '/admin' => 'admin',
    '/test' => 'Example.php',
    '/test/([^/]+)/(\d+)' => array('Example.php', 'homepage'), // /test/lol/123
);