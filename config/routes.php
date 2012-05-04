<?php
/**
 * Routes configuration
 *
 * string rule => string|array routes
 * if route first symbol is ! - this is controller (!class:method)
 */
return array(
    '/(?:index\.html|page_(\d+)\.html)?' => array('!common:session', '!site:news', 'homepage'),
    '/(\d+)\-\S+\.html' => array('!common:session', '!site:showPost', 'homepage'),
    '/openauth' => array('!common:session', '!site:openAuth'),
    '/logout' => array('!common:session', '!site:logout'),
    '/test' => '!common:test',
);