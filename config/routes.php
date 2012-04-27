<?php
/**
 * Routes configuration
 *
 * string rule => string|array routes
 * if route first symbol is ! - this is controller (!class:method)
 */
return array(
    '/(?:index\.html|page_(\d+)\.html)?' => array('!site:news', 'homepage'),
    '/test' => '!common:test',
);