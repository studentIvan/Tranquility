<?php
class Admin
{
    public static function control($matches)
    {
        $page = isset($matches[1]) ?
            preg_replace('/[^a-z]/', '', $matches[1]) : false;
        $express = isset($matches[2]) ?
            preg_replace('/[^a-z]/', '', $matches[2]) : false;
        Process::$context['admin_page'] = $page;
        if (method_exists('Admin', $page)) {
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
                case 'news':
                    if (!isset(Process::$context['cms']['news']))
                        throw new NotFoundException();

                    if (!$action)
                    {
                        $perPage = isset(Process::$context['cms']['news']['limit_per_page']) ?
                            Process::$context['cms']['news']['limit_per_page'] : 20;
                        $pagination = Data::paginate(Database::count('news'), $perPage, $page);
                        Process::$context['news_list'] = News::listing($pagination['offset'], $perPage);
                        Process::$context['pagination'] = ($pagination['total_pages'] > 1) ? $pagination : false;
                        Process::$context['data_title'] = 'Новостной блог';
                        Process::$context['custom_content_view'] = true;
                    }
                    elseif ($action == 'new')
                    {
                        Process::$context['data_title'] = 'Новостной блог (новая запись)';
                        list($title, $content, $tags) = Data::inputsList('title', 'content', 'tags');

                        if (($title !== false) and ($content !== false) and ($tags !== false))
                        {
                            if (News::create(Session::getUid(), $title, $content, $tags))
                            {
                                header('Location: /admin/manager/news');
                                exit;
                            }
                        }
                    }
                    elseif ($action == 'select' and $identify)
                    {
                        $post = News::getObjectById($identify);
                        Process::$context['data_title'] = $post->title;
                        Process::$context['object_content'] = $post->content;
                        Process::$context['object_created_at'] = $post->created_at;
                        Process::$context['object_identify'] = $identify;
                    }
                    else
                    {
                        throw new NotFoundException();
                    }


                    break;

                case 'users':
                    if (!isset(Process::$context['cms']['users']))
                        throw new NotFoundException();
                    $perPage = isset(Process::$context['cms']['users']['limit_per_page']) ?
                        Process::$context['cms']['users']['limit_per_page'] : 20;
                    $pagination = Data::paginate(Database::count('users'), $perPage, $page);
                    Process::$context['users_list'] = Users::listing($pagination['offset'], $perPage);
                    Process::$context['pagination'] = ($pagination['total_pages'] > 1) ? $pagination : false;
                    Process::$context['data_title'] = 'Пользователи';
                    break;

                case 'acl':
                    if (!isset(Process::$context['cms']['users']))
                        throw new NotFoundException();
                    Process::$context['roles_list'] = Roles::listing();
                    Process::$context['data_title'] = 'Роли пользователей';
                    break;

                case 'sessions':
                    if (!isset(Process::$context['cms']['sessions']))
                        throw new NotFoundException();
                    $perPage = isset(Process::$context['cms']['sessions']['limit_per_page']) ?
                        Process::$context['cms']['sessions']['limit_per_page'] : 20;
                    $pagination = Data::paginate(Database::count('sessions'), $perPage, $page);
                    Process::$context['sessions_list'] = Session::getAll($pagination['offset'], $perPage);
                    Process::$context['pagination'] = ($pagination['total_pages'] > 1) ? $pagination : false;
                    Process::$context['data_title'] = 'Монитор сессий';
                    Process::$context['user_token'] = Session::getToken();
                    break;

                default:
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
        if (Session::getRole() == 1) {
            Session::stop();
            header('Location: /admin/');
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
