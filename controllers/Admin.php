<?php
class Admin
{
    public static function test()
    {
        echo Database::getInstance()
            ->query('SELECT MD5(123) as hello')
            ->fetch(PDO::FETCH_OBJ)->hello;
    }
}
