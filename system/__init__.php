<?php
/**
 * Tranquility initial script
 *
 * Separated by sharp region directives
 * @see http://msdn.microsoft.com/en-us/library/67w7t67f.aspx
 */

#region Loading configuration
$config = require __DIR__ . '/../config/config.php';
date_default_timezone_set($config['server_timezone']);

define('STARTED_AT', microtime(true));
define('DEVELOPER_MODE', isset($config['developer_mode']) ? $config['developer_mode'] : false);
$__DIR__ = dirname(__FILE__);
#endregion

#region Exceptions
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
#endregion

#region System classes - include and init
require_once $__DIR__ . '/classes/Database.php';
require_once $__DIR__ . '/classes/Security.php';
require_once $__DIR__ . '/classes/Cookies.php';
require_once $__DIR__ . '/classes/Session.php';
require_once $__DIR__ . '/classes/Services.php';
require_once $__DIR__ . '/classes/Data.php';

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
#endregion

#region Process class
class Process
{
    public static
        $context = array(),
        $routes = array(),
        $solutions = array();

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
            $__DIR__ = dirname(__FILE__);
            include_once $__DIR__ . '/../vendor/Twig/Autoloader.php';

            Twig_Autoloader::register();

            $fileSystem = array($__DIR__ . '/../views');
            foreach (self::$solutions as $__solution) {
                $fileSystem[] = "$__DIR__/../solutions/$__solution/views";
            }

            $loader = new Twig_Loader_Filesystem($fileSystem);
            self::$twig = new Twig_Environment($loader, array(
                'cache' => DEVELOPER_MODE ? false : $__DIR__ . '/cache/twig',
            ));

            include_once $__DIR__ . '/classes/TwigEx.php';
            self::$twig->addExtension(new TwigEx());
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
            $split = explode(':', $route);

            if (!isset($split[2])) {
                list($class, $method) = $split;
                if (!class_exists($class, false)) {
                    include dirname(__FILE__) . "/../controllers/$class.php";
                }
            } else {
                list($solution, $class, $method) = $split;
                $class = ucfirst($class);
                if (!class_exists($class, false)) {
                    include dirname(__FILE__) . "/../solutions/$solution/controllers/$class.php";
                }
            }

            call_user_func(array($class, $method), $matches);
        }
        else
        {
            $twig = self::getTwigInstance();
            echo $twig->render("$route.html.twig", self::$context);
        }

        self::$state++;
    }

    /**
     * @static
     * @param string $helper
     */
    protected static function loadHelper($helper) {
        include_once dirname(__FILE__) . '/../helpers/' . ucfirst($helper) . '.php';
    }

    /**
     * @static
     * @param string|array $helpers
     */
    public static function load($helpers)
    {
        if (is_array($helpers)) {
            foreach ($helpers as $helper) self::loadHelper($helper);
        } else {
            self::loadHelper($helpers);
        }
    }

    /**
     * @static
     * @param string $location
     */
    public static function redirect($location) {
        header("Location: $location");
        exit;
    }
}
#endregion

#region Initial routes
Process::$routes = require $__DIR__ . '/../config/routes.php';
#endregion

#region Solutions
if (isset($config['solutions'])) {
    Process::$solutions = $config['solutions'];
    foreach (Process::$solutions as $_solution) {
        if (file_exists("$__DIR__/../solutions/$_solution/__init__.php")) {
            require_once  "$__DIR__/../solutions/$_solution/__init__.php";
        }
        if (file_exists("$__DIR__/../solutions/$_solution/routes.php")) {
            Process::$routes += require "$__DIR__/../solutions/$_solution/routes.php";
        }
    }
}
#endregion

#region Application
require_once $__DIR__ . '/../__init__.php';
#endregion

if (isset($_SERVER['REQUEST_URI'])) {
    #region Routing and dispatching
    if (!isset(Process::$context['mobile'])) {
        Process::$context['mobile'] =
            (isset($config['always_mobile']) and $config['always_mobile']) ?
                true : require $__DIR__ . '/ismobile.php';
    }

    Process::$context['resource'] = isset($config['resources']) ? $config['resources'] : array();
    Process::$context['uri'] = htmlspecialchars($_SERVER['REQUEST_URI']);
    Process::$context['cms'] = isset($config['cms']) ? $config['cms'] : array();
    Process::$context['page_title'] = isset(Process::$context['page_title']) ?
        Process::$context['page_title'] : (isset($config['default_site_title']) ?
            $config['default_site_title'] : '');

    if (($pos = strpos(Process::$context['uri'], '?')) !== false) {
        Process::$context['uri'] = substr(Process::$context['uri'], 0, $pos);
    }

    try
    {
        if (isset($_GET['e']) and $_GET['e'] == 403) throw new ForbiddenException();
        foreach (Process::$routes as $rule => $route)
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
            Process::$context['page_title'] = '404 Not Found';
            $twig->display('404.html.twig', Process::$context);
        }
        elseif ($e->getCode() == 403) {
            header("HTTP/1.0 403 Forbidden");
            Process::$context['page_title'] = '403 Forbidden';
            $twig->display('403.html.twig', Process::$context);
        }
        else
        {
            if (DEVELOPER_MODE)
            {
                /*$exceptMessage = (strlen($e->getMessage()) > 70) ?
                    '...' . substr($e->getMessage(), -70, 70) : $e->getMessage();*/

                Process::$context['exception'] = array(
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'message' => $e->getMessage(),
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
    #endregion
}
