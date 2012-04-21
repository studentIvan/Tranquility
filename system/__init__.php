<?php
$config = require __DIR__ . '/../config/config.php';

define('STARTED_AT', microtime(true));
define('DEVELOPER_MODE', isset($config['developer_mode']) ? $config['developer_mode'] : false);

class AuthException extends Exception {

}

class SessionException extends Exception {

}

class NotFoundException extends Exception {
    public function __construct($message = '') {
        parent::__construct($message, 404);
    }
}

class ForbiddenException extends Exception {
    public function __construct($message = '') {
        parent::__construct($message, 403);
    }
}

function exception_error_handler($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

set_error_handler("exception_error_handler");

require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/Security.php';
require_once __DIR__ . '/classes/Cookies.php';
require_once __DIR__ . '/classes/Session.php';
require_once __DIR__ . '/classes/Services.php';
require_once __DIR__ . '/classes/Data.php';

if (isset($config['cms']))
{
    if (isset($config['cms']['news']) and $config['cms']['news']) {
        require_once __DIR__ . '/solutions/News.php';
    }

    if (isset($config['cms']['users']) and $config['cms']['users']) {
        require_once __DIR__ . '/solutions/Users.php';
        require_once __DIR__ . '/solutions/Roles.php';
    }
}

try
{
    Database::setConfiguration($config['pdo']['dsn'], $config['pdo']['username'], $config['pdo']['password']);
    Security::setSecret($config['security_token']);
    Session::setConfiguration($config['session']);
    $config['pdo'] = $config['security_token'] = null;
}
catch (Exception $e)
{
    echo 'Fatal error';
    exit;
}