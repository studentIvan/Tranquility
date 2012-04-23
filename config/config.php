<?php
/**
 * Associative array
 */
return array(
    /**
     * Developer Mode
     * - no twig cache
     * - dev resources
     * - page execution time after page (commented)
     */
    'developer_mode' => true,

    /**
     * Mobile template detection configuration
     * Set always_mobile as true for using permament mobile templates
     * Default false
     */
    'always_mobile' => false,

    /**
     * CDN configuration
     * http://api.yandex.ru/jslibs/?ncrnd=7105
     */
    'resources' => array(
        'ldt_js' => '/js/ldt.min.js',
        'jquery_js' => 'http://yandex.st/jquery/1.7.2/jquery.min.js',
        'jquery_mobile_js' => 'http://yandex.st/jquery/mobile/1.1.0/jquery.mobile.min.js',
        'jquery_mobile_css' => 'http://yandex.st/jquery/mobile/1.1.0/jquery.mobile.min.css',
        'jquery_mobile_structure_css' => 'http://yandex.st/jquery/mobile/1.1.0/jquery.mobile.structure.min.css',
        'jquery_mobile_theme_css' => 'http://yandex.st/jquery/mobile/1.1.0/jquery.mobile.theme.min.css',
        'jquery_fancybox_js' => 'http://yandex.st/jquery/fancybox/2.0.5/jquery.fancybox.min.js',
        'jquery_fancybox_css' => 'http://yandex.st/jquery/fancybox/2.0.5/jquery.fancybox.min.css',
    ),

    /**
     * PDO configuration
     */
    'pdo' => array(
        'dsn' => 'mysql:host=localhost;dbname=turbo',
        'username' => 'root',
        'password' => '',
    ),

    /**
     * Security token for authorisation, authentication and some other operations
     */
    'security_token' => 'ololo',

    /**
     * Session configuration
     */
    'session' => array(
        'bot_role' => 5,
        'guest_role' => 4,
        'lifetime_hours' => 1,
        'referers' => 4, // store referers N days, or false for offstore
        'garbage_auto_dump' => true, // Call dumpGarbage() every EVEN minute
                                     // Recommended: false (but it need cronjob configuration)
    ),

    /**
     * Content Management System configuration
     * You can mark component as false for off it
     */
    'cms' => array(
        'news' => array('limit_per_page' => 10),
        'sessions' => array('limit_per_page' => 20),
        'users' => array('limit_per_page' => 20),
    ),
);