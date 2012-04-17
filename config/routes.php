<?php
/**
 * Routes configuration
 *
 * string rule => string|array routes
 * if route first symbol is ! - this is controller (!class:method)
 */
return array(
    '/' => 'homepage',
    '/admin' => 'admin',
    '/admin/test' => '!admin:test',
    '/test' => '!example:call',
    '/test/([^/]+)/(\d+)' => array('!example:call', 'homepage'), // /test/lol/123
);