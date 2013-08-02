<?php
class Admin
{
    protected static $checkCSRFToken = false;
    protected static $configuration = array();
    protected static $models = array();

    public static function control($matches)
    {
        if (Session::getRole() !== 1) {
            Process::$context['bot_secure'] = array(
                'input_login' => Security::getUniqueDigestForUserIP('input_login'),
                'input_pass' => Security::getUniqueDigestForUserIP('input_pass'),
                'input_check' => Security::getUniqueDigestForUserIP('input_check'),
            );

            list($login, $password, $remember) =
                Data::inputsList(
                    Process::$context['bot_secure']['input_login'],
                    Process::$context['bot_secure']['input_pass'],
                    Process::$context['bot_secure']['input_check']
                );

            if ($login and $password) {
                $temporary = $remember ? false : true;
                try {
                    Session::authorize($login, $password, $temporary);
                    if (Session::getRole() !== 1) {
                        throw new AuthException('role error');
                    } else {
                        self::load($matches);
                    }
                } catch (Exception $e) {
                    Process::$context['flash_error'] = true;
                    Process::getTwigInstance()->display('admin/login.html.twig', Process::$context);
                }
            } else {
                Process::getTwigInstance()->display('admin/login.html.twig', Process::$context);
            }
        } else {
            self::load($matches);
        }
    }

    public static function load($matches)
    {
        $partition = isset($matches[1]) ?
            preg_replace('/[^a-z]/', '', $matches[1]) : false;
        $action = isset($matches[2]) ?
            preg_replace('/[^a-z0-9_]/', '', $matches[2]) : false;

        Process::$context['admin_part'] = $partition;
        Process::$context['admin_part_action'] = $action;

        /**
         * check CSRF Token (optionally)
         */
        self::$checkCSRFToken = (isset($_GET['csrf_token']) and
            Process::$context['csrf_token'] === $_GET['csrf_token']);

        /**
         * Loading configuration
         */
        self::$configuration = Process::$context['cms']['admin_cfg'];

        if ($partition === 'logout') {
            if (self::$checkCSRFToken) {
                Session::stop();
                Process::redirect(self::$configuration['base_uri']);
            } else {
                throw new ForbiddenException();
            }
        }

        if ($partition === 'ajax') {
            if (self::$checkCSRFToken) {
                /**
                 * POST AJAX DATA
                 */
                echo 'test';
                exit;
            } else {
                throw new ForbiddenException();
            }
        }

        /**
         * Init models
         */
        Process::$context['admin_menu_elements'] = array(array(
            'name' => 'Главная', 'uri' => '', 'icon' => 'icon-home',
        ));

        include_once dirname(__FILE__) . '/../crud/CRUDObject.php';
        foreach (self::$configuration['registered_crud'] as $crud) {
            if (!class_exists($crud)) {
                $target = dirname(__FILE__) . '/../crud/' . $crud . '.php';
                if (file_exists($target)) {
                    include_once $target;
                } else {
                    throw new Exception("CRUD $crud not exists");
                }
            }

            /**
             * @var $p CRUDObject
             */
            $p = new $crud();

            if (($p instanceof CRUDObject) === false)
                throw new Exception("CRUD $p is not instance of CRUDObject");

            self::$models[] = $p;
            Process::$context['admin_menu_elements'][] = $p->getInfo();
        }

        Process::$context['page_title'] = 'Админ панель';
        Process::$context['panel_base_uri'] = self::$configuration['base_uri'];
        if (!isset(Process::$context['current_user'])) {
            Process::$context['current_user'] = array(
                'login' => Data::input(Process::$context['bot_secure']['input_login'])
            );
        }

        Process::$context['container'] = self::getContainer($partition, $action);
        Process::getTwigInstance()->display('admin/admin.html.twig', Process::$context);
    }

    public static function getContainer($partition, $action)
    {
        $container = array(
            'type' => 'text',
            'text' => 'Выберите интересующий раздел'
        );

        if ($partition)
        {


            foreach (self::$models as $m)
            {
                /**
                 * @var $m CRUDObject
                 */
                if ($m->getMenuURI() === $partition)
                {
                    if ($action and preg_match('/page_(\d+)/', $action, $matches)) {
                        $page = $matches[1]*1;
                    } else {
                        $page = 1;
                    }

                    $count = $m->getCount();
                    $perPage = $m->getElementsPerPageNum();
                    $pagination = Data::paginate($count, $perPage, $page);
                    $container['type'] = 'listing';
                    $container['count'] = ($count > $perPage) ? $perPage : $count;
                    $container['diff_field'] = $m->getDiffField();
                    $container['fields_displayable'] = $m->getDisplayable();
                    $container['fields'] = $m->getFields();
                    $container['data'] = $m->getListing($pagination['offset'], $perPage);
                    $container['create_new_message'] = $m->getCreateString();
                    foreach ($container['fields'] as $f => $d) {
                        if ($d['modify']) {
                            foreach ($container['data'] as &$e) {
                                $e[$f] = str_replace('$1', $e[$f], $d['modify']);
                            }
                        }
                    }
                    Process::$context['pagination'] = ($pagination['total_pages'] > 1) ? $pagination : false;
                }
            }
        }

        return $container;
    }
}
