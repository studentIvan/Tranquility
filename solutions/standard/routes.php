<?php
/**
 * Standard routes
 */
return array(
    '/admin(?:/([^/]+))?(?:/([^/]+))?/?' => array('!common:session', '!standard:admin:dispatcher'),
);