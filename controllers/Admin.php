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

        if ($component)
        {
            Process::$context['component'] = $component;
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
