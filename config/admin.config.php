<?php
/**
 * Associative array
 */
return array(
    /**
     * Backend URI (experimental settings)
     */
    'base_uri' => '/admin',

    /**
     * User Roles IDs, who can visit the admin-panel
     */
    'access_roles' => array(1, 2),

    /**
     * Enumeration of admin-panel crud-partitions
     */
    'registered_crud' => array(
        'CRUDNews', 'CRUDNewsComments', 'CRUDUsers',
        'CRUDUsersData', 'CRUDRoles', 'CRUDReferrers', 'CRUDSessions'
    )
);