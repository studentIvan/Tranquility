<?php
class Admin
{
    public static function secure()
    {
        echo Session::getRole();
        /*if (Session::getRole() !== 1) {
            Process::getTwigInstance()->display('admin/login.html.twig', Process::$context);
        } else {
            Process::getTwigInstance()->display('admin/admin.html.twig', Process::$context);
        }*/
    }
}
