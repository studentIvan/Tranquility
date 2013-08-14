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
     * Included solutions from solutions folder
     * Note: you may set this parameter as false
     */
    'solutions' => array('standard', 'profiles', 'installer'),

    /**
     * Mobile template detection configuration
     * Set always_mobile as true for using permament mobile templates
     * Default false
     */
    'always_mobile' => false,

    /**
     * Server timezone (e.g. Europe/Moscow +04:00)
     */
    'server_timezone' => 'Europe/Moscow',

    /**
     * CDN configuration
     * http://api.yandex.ru/jslibs/?ncrnd=7105
     */
    'resources' => array(
        'ldt_js' => '/js/ldt.min.js',
        'bootstrap_js' => '/js/bootstrap.min.js',
        'bootstrap_css' => '/css/bootstrap.min.css',
        'html5shim_js' => 'http://html5shim.googlecode.com/svn/trunk/html5.js',
        'batman_hand_js' => '/js/batman_hand.min.js',

        /**
         * JQuery
         */
        //'jquery_js' => 'http://yandex.st/jquery/2.0.3/jquery.min.js',
        'jquery_js' => '/js/jquery.min.js',
        'jquery_tablesorter_js' => '/js/jquery.tablesorter.min.js',
        'jquery_tinymce_js' => '/js/tiny_mce/jquery.tinymce.js',
        'jquery_mobile_js' => 'http://yandex.st/jquery/mobile/1.1.0/jquery.mobile.min.js',
        'jquery_mobile_css' => 'http://yandex.st/jquery/mobile/1.1.0/jquery.mobile.min.css',
        'jquery_mobile_structure_css' => 'http://yandex.st/jquery/mobile/1.1.0/jquery.mobile.structure.min.css',
        'jquery_mobile_theme_css' => 'http://yandex.st/jquery/mobile/1.1.0/jquery.mobile.theme.min.css',
        'jquery_fancybox_js' => 'http://yandex.st/jquery/fancybox/2.0.5/jquery.fancybox.min.js',
        'jquery_fancybox_css' => 'http://yandex.st/jquery/fancybox/2.0.5/jquery.fancybox.min.css',

        /**
         * http://softwaremaniacs.org/media/soft/highlight/test.html
         */
        'highlightjs_arta_css' => 'http://yandex.st/highlightjs/6.1/styles/arta.min.css',
        'highlightjs_default_css' => 'http://yandex.st/highlightjs/6.1/styles/default.min.css',
        'highlightjs_zenburn_css' => 'http://yandex.st/highlightjs/6.1/styles/zenburn.min.css',
        'highlightjs_far_css' => 'http://yandex.st/highlightjs/6.1/styles/far.min.css',
        'highlightjs_idea_css' => 'http://yandex.st/highlightjs/6.1/styles/idea.min.css',
        'highlightjs_sunburst_css' => 'http://yandex.st/highlightjs/6.1/styles/sunburst.min.css',
        'highlightjs_github_css' => 'http://yandex.st/highlightjs/6.1/styles/github.min.css',
        'highlightjs_ascetic_css' => 'http://yandex.st/highlightjs/6.1/styles/ascetic.min.css',
        'highlightjs_magula_css' => 'http://yandex.st/highlightjs/6.1/styles/magula.min.css',
        'highlightjs_js' => 'http://yandex.st/highlightjs/6.1/highlight.min.js',
    ),

    /**
     * PDO configuration (developer)
     */
    'pdo_developer_mode' => array(
        'dsn' => 'mysql:host=localhost;dbname=tranquility',
        'username' => 'root',
        'password' => '123456',
    ),

    /**
     * PDO configuration (production)
     */
    'pdo_production_mode' => array(
        'dsn' => 'mysql:host=localhost;dbname=tranquility',
        'username' => 'root',
        'password' => '123456',
    ),

    /**
     * Security token for authorisation, authentication and some other operations
     */
    'security_token' => 'ololo',

    /**
     * Default site title
     */
    'default_site_title' => 'Tranquility site',

    /*
     * Free space on your hosting in mb
     */
    'hosting_free_space_mb' => 100,

    /**
     * Visitors clear INTERVAL configuration
     */
    'save_visitors_for' => '1 YEAR',

    /**
     * Session configuration
     */
    'session' => array(
        'bot_role' => 5,
        'guest_role' => 4,
        'lifetime_hours' => 1,
        'referrers' => 4, // store referers N days, or false for offstore
        'garbage_auto_dump' => true, // Call dumpGarbage() every EVEN minute
        // Recommended: false (but it need cronjob configuration)
    ),

    /**
     * Content Management System configuration
     * You can mark component as false for off it
     */
    'cms' => array(
        'news' => array(
            'limit_per_page' => 2,
        ),
        'email_confirm' => false,
    ),
);