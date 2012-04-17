<?php
class Admin
{
    public static function test()
    {
        Session::start();

        echo Session::getToken();

        //Session::stop();
    }
}
