<?php
class Admin
{
    protected static $checkCsrfToken = false;

    public static function control($matches)
    {
        $page = isset($matches[1]) ?
            preg_replace('/[^a-z]/', '', $matches[1]) : false;
        $express = isset($matches[2]) ?
            preg_replace('/[^a-z]/', '', $matches[2]) : false;
        Process::$context['admin_page'] = $page;
        if (method_exists('Admin', $page)) {
            self::$checkCsrfToken = (isset($_GET['csrf_token']) and
                Process::$context['csrf_token'] === $_GET['csrf_token']);
            call_user_func(array('Admin', $page), ($express ? $express : null));
        } else {
            self::secure();
        }
    }

    public static function manager($component = false)
    {
        if (Session::getRole() !== 1)
        {
            header('Location: /admin/');
            exit;
        }

        Process::$context['data_title'] = 'Управление';

        if ($component and isset(Process::$context['cms']))
        {
            Process::$context['component'] = $component;
            $page = isset($_GET['page']) ? abs($_GET['page']) : 1;
            $action = isset($_GET['action']) ? $_GET['action'] : false;
            $identify = isset($_GET['identify']) ? abs($_GET['identify']) : false;

            Process::$context['data_action'] = $action;

            switch($component)
            {
                case 'cronstress':
                    if (self::$checkCsrfToken) {
                        include_once dirname(__FILE__) . '/../../../system/cronjob.php';
                        header('Location: /admin/manager?cron_result=ok');
                        exit;
                    } else {
                        throw new ForbiddenException();
                    }

                    break;

                case 'news':
                    if (!isset(Process::$context['cms']['news']))
                        throw new NotFoundException();

                    if (!$action)
                    {
                        $perPage = 20;
                        $pagination = Data::paginate(Database::count('news'), $perPage, $page);
                        Process::$context['news_list'] = News::listing($pagination['offset'], $perPage);
                        Process::$context['pagination'] = ($pagination['total_pages'] > 1) ? $pagination : false;
                        Process::$context['data_title'] = 'Новостной блог';
                        Process::$context['custom_content_view'] = true;
                    }
                    elseif ($action == 'new')
                    {
                        Process::$context['data_title'] = 'Новостной блог (новая запись)';
                        list($title, $content, $tags) = Data::inputsList('titlen', 'contentn', 'tagsn');

                        if (($title !== false) and ($content !== false)
                            and ($tags !== false) and self::$checkCsrfToken)
                        {
                            if (News::create(Session::getUid(), $title, $content, $tags))
                            {
                                header('Location: /admin/manager/news');
                                exit;
                            }
                        }
                    }
                    elseif ($action == 'edit' and $identify and self::$checkCsrfToken)
                    {
                        list($title, $content, $tags) = Data::inputsList('title', 'content', 'tags');

                        if (($title !== false) and ($content !== false) and ($tags !== false)
                            and News::edit($identify, $title, $content, $tags))
                        {
                            header('Location: /admin/manager/news?action=select&identify=' . $identify);
                            exit;
                        }
                        else
                        {
                            $post = News::getObjectById($identify);
                            Process::$context['data_title'] = $post->title . ' (редактирование)';
                            Process::$context['object_title'] = $post->title;
                            Process::$context['object_content'] = $post->content;
                            Process::$context['object_tags'] = $post->tags;
                            Process::$context['object_identify'] = $identify;
                        }
                    }
                    elseif ($action == 'select' and $identify)
                    {
                        $post = News::getObjectById($identify);
                        Process::$context['data_title'] = $post->title;
                        Process::$context['object_content'] = $post->content;
                        Process::$context['object_created_at'] = $post->created_at;
                        Process::$context['object_identify'] = $identify;
                        Process::$context['custom_content_view'] = true;
                    }
                    elseif ($action == 'delete' and $identify and self::$checkCsrfToken)
                    {
                        News::remove($identify);
                        header('Location: /admin/manager/news?no_cache=' . md5(rand(111, 999)));
                        exit;
                    }
                    else
                    {
                        throw new NotFoundException();
                    }

                    break;

                case 'users':
                    if (!isset(Process::$context['cms']['users']))
                        throw new NotFoundException();

                    Process::$context['data_title'] = 'Пользователи';

                    if (!$action)
                    {
                        $perPage = 20;
                        $pagination = Data::paginate(Database::count('users'), $perPage, $page);
                        Process::$context['users_list'] = Users::listing($pagination['offset'], $perPage);
                        Process::$context['pagination'] = ($pagination['total_pages'] > 1) ? $pagination : false;
                    }
                    elseif ($action == 'add' and self::$checkCsrfToken)
                    {
                        list($login, $password, $roleId) =
                            Data::inputsList('login', 'password', 'role');

                        if ($login and $password and $roleId and
                            Users::create($login, Security::getDigest($password), $roleId)) {
                            header(
                                'Location: /admin/manager/users?no_cache=' .
                                    Security::getDigest(rand(100, 999))
                            );
                            exit;
                        } else {
                            Process::$context['roles_list'] = Roles::listing();
                        }
                    }
                    elseif ($action == 'edit' and $identify and self::$checkCsrfToken)
                    {
                        list($login, $passwordChange, $password, $roleId) =
                            Data::inputsList('login', 'pchng', 'password', 'role');

                        if ($passwordChange and $password and $passwordChange == 'on') {
                            $password = Security::getDigest($password);
                        } else {
                            $sql = "SELECT password FROM users WHERE id=$identify";
                            $password = strval(Database::getSingleResult($sql));
                        }

                        if ($login and $password and $roleId and
                            Users::edit($identify, $login, $password, $roleId)) {
                            header(
                                "Location: /admin/manager/users?action=select&identify=$identify"
                            );
                            exit;
                        } else {
                            Process::$context['roles_list'] = Roles::listing();
                            $user = Users::getObjectById($identify);
                            Process::$context['user_role'] = $user->role;
                            Process::$context['user_login'] = $user->login;
                            Process::$context['user_identify'] = $identify;
                        }
                    }
                    elseif ($action == 'delete' and $identify and self::$checkCsrfToken)
                    {
                        Users::remove($identify);
                        header('Location: /admin/manager/users?no_cache=' . md5(rand(111, 999)));
                        exit;
                    }
                    elseif ($action == 'select' and $identify)
                    {
                        $user = Users::getObjectById($identify);
                        Process::$context['user_title'] = $user->title;
                        Process::$context['user_login'] = $user->login;
                        Process::$context['user_password'] = $user->password;
                        Process::$context['user_registered_at'] = $user->registered_at;
                        Process::$context['user_identify'] = $identify;
                        Process::$context['custom_content_view'] = true;
                    }
                    else
                    {
                        throw new NotFoundException();
                    }

                    break;

                case 'acl':
                    if (!isset(Process::$context['cms']['users']))
                        throw new NotFoundException();

                    Process::$context['data_title'] = 'Роли пользователей';
                    Process::$context['session_cfg'] = Session::getOptions();

                    if (!$action)
                    {
                        Process::$context['roles_list'] = Roles::listing();
                    }
                    elseif ($action == 'add' and self::$checkCsrfToken)
                    {
                        if ($title = Data::input('title')) {
                            Roles::create($title);
                            header("Location: /admin/manager/acl");
                            exit;
                        }
                    }
                    elseif ($action == 'edit' and $identify and self::$checkCsrfToken)
                    {
                        if ($title = Data::input('title')) {
                            Roles::edit($identify, $title);
                            header("Location: /admin/manager/acl");
                            exit;
                        } else {
                            throw new NotFoundException();
                        }
                    }
                    elseif ($action == 'delete' and $identify and self::$checkCsrfToken)
                    {
                        Roles::remove($identify);
                        header('Location: /admin/manager/acl?no_cache=' . md5(rand(111, 999)));
                        exit;
                    }
                    elseif ($action == 'select' and $identify)
                    {
                        $role = Roles::getObjectById($identify);
                        Process::$context['object_identify'] = $role->id;
                        Process::$context['object_title'] = $role->title;
                    }
                    else
                    {
                        throw new NotFoundException();
                    }

                    break;

                case 'sessions':
                    if (!isset(Process::$context['cms']['sessions']))
                        throw new NotFoundException();

                    $perPage = 20;
                    $pagination = Data::paginate(Database::count('sessions'), $perPage, $page);
                    Process::$context['sessions_list'] = Session::getAll($pagination['offset'], $perPage);
                    Process::$context['pagination'] = ($pagination['total_pages'] > 1) ? $pagination : false;
                    Process::$context['data_title'] = 'Монитор сессий';
                    Process::$context['user_token'] = Session::getToken();

                    break;

                default:
                    throw new NotFoundException();
            }
        }
        else
        {
            Process::$context['news_count'] = Database::count('news');
            Process::$context['users_count'] = Database::count('users');
            Process::$context['sessions_count'] = Database::count('sessions');
            Process::$context['roles_count'] = Database::count('roles');
        }

        Process::getTwigInstance()->display('admin/admin.html.twig', Process::$context);
    }

    public static function secure()
    {
        if (Session::getRole() !== 1) {
            Process::getTwigInstance()->display('admin/login.html.twig', Process::$context);
        } else {
            Process::getTwigInstance()->display('admin/admin.html.twig', Process::$context);
        }
    }

    public static function logout()
    {
        if (Session::getRole() == 1 and self::$checkCsrfToken)
        {
            Session::stop();
            header('Location: /admin/');
        }
        else
        {
            throw new ForbiddenException();
        }
    }

    public static function login()
    {
        if (Session::getRole() !== 1)
        {
            list($login, $password, $remember) =
                Data::inputsList('xcya94n8cdjscam', 'asdj91n43fbdsvas0o4', 'remember');

            $temporary = $remember ? false : true;

            try {
                Session::authorize($login, $password, $temporary);
                header('Location: /admin/manager/');
            } catch (Exception $e) {
                Process::$context['flash_error'] = true;
                Process::getTwigInstance()->display('admin/login.html.twig', Process::$context);
            }
        }
        else
        {
            Process::getTwigInstance()->display('admin/admin.html.twig', Process::$context);
        }
    }
}
