<?php
$config = require __DIR__ . '/../config/config.php';
date_default_timezone_set('Europe/Moscow');

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

class Process
{
    /**
     * @var array
     */
    public static $context = array();

    /**
     * @var Twig_Environment
     */
    private static $twig = null;

    /**
     * @var int
     */
    public static $state = 0;

    /**
     * @static
     * @return Twig_Environment
     */
    public static function getTwigInstance()
    {
        if (is_null(self::$twig))
        {
            include_once __DIR__ . '/../vendor/Twig/Autoloader.php';

            Twig_Autoloader::register();

            $loader = new Twig_Loader_Filesystem(__DIR__ . '/../views');
            self::$twig = new Twig_Environment($loader, array(
                'cache' => DEVELOPER_MODE ? false : __DIR__ . '/cache',
            ));

            include_once __DIR__ . '/classes/Twig_i18nPlural.php';
            self::$twig->addExtension(new Twig_i18nPlural());
        }

        return self::$twig;
    }

    /**
     * @static
     * @param $route
     * @param $matches
     */
    public static function callRoute($route, $matches)
    {
        if (substr($route, 0, 1) == '!')
        {
            $route = ucfirst(substr($route, 1));
            list($class, $method) = explode(':', $route);
            include __DIR__ . "/../controllers/$class.php";
            call_user_func(array($class, $method), $matches);
        }
        else
        {
            $twig = self::getTwigInstance();
            echo $twig->render("$route.html.twig", self::$context);
        }

        self::$state++;
    }
}

if (isset($_SERVER['REQUEST_URI']))
{
    Process::$context['mobile'] =
        (isset($config['always_mobile']) and $config['always_mobile']) ?
            true : require __DIR__ . '/ismobile.php';

    Process::$context['resource'] = isset($config['resources']) ? $config['resources'] : array();
    Process::$context['uri'] = htmlspecialchars($_SERVER['REQUEST_URI']);
    Process::$context['cms'] = isset($config['cms']) ? $config['cms'] : array();

    if (($pos = strpos(Process::$context['uri'], '?')) !== false) {
        Process::$context['uri'] = substr(Process::$context['uri'], 0, $pos);
    }

    try
    {
        if (isset($_GET['e']) and $_GET['e'] == 403) throw new ForbiddenException();
        foreach (require __DIR__ . '/../config/routes.php' as $rule => $route)
        {
            if (preg_match('/^' . str_replace('/', '\/', $rule) . '$/', Process::$context['uri'], $matches))
            {
                if (is_array($route)) {
                    foreach ($route as $subroute) Process::callRoute($subroute, $matches);
                } else {
                    Process::callRoute($route, $matches);
                }
            }
        }

        if (Process::$state == 0)
            throw new NotFoundException();
    }
    catch (Exception $e)
    {
        $twig = Process::getTwigInstance();
        if ($e->getCode() == 404) {
            header("HTTP/1.0 404 Not Found");
            $twig->display('404.html.twig', Process::$context);
        }
        elseif ($e->getCode() == 403) {
            header("HTTP/1.0 403 Forbidden");
            $twig->display('403.html.twig', Process::$context);
        }
        else
        {
            if (DEVELOPER_MODE)
            {
                $exceptMessage = (strlen($e->getMessage()) > 70) ?
                    '...' . substr($e->getMessage(), -70, 70) : $e->getMessage();

                Process::$context['exception'] = array(
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $exceptMessage,
                    'trace' => $e->getTraceAsString(),
                );
            }
            else
            {
                Process::$context['exception'] = array(
                    'file' => false,
                    'line' => false,
                    'message' => 'Упс! Ошибочка',
                    'trace' => 'На сайте произошла ошибка, администрация сайта уже уведомлена об этом',
                );
            }

            $twig->display('exception.html.twig', Process::$context);
        }
    }
}