<?php
class Admin
{
    public static function test()
    {
        Session::start();
        echo Session::getToken();
        echo '<pre>';
        print_r($_COOKIE);
    }
}
