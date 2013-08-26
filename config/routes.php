<?php
/**
 * Routes configuration
 *
 * string rule => string|array routes
 * if route first symbol is ! - this is controller (!class:method)
 */
return array(
    '/(?:index\.html|page_(\d+)\.html)?' => array('!common:session', '!site:news', 'homepage'),
    '/tag/(\S+)' => array('!common:session', '!site:tag', 'widgets/tag'),
    '/(\d+)\-\S+\.html' => array('!common:session', '!site:showPost', 'homepage'),
    '/openauth' => array('!common:session', '!site:openAuth'),
    '/ajax_social_session_dispatcher' => array('!common:session', '!site:socialDispatcher'),
    '/login' => array('!common:session', '!site:login'),
    '/logout' => array('!common:session', '!site:logout'),
    '/test' => '!common:test',
);