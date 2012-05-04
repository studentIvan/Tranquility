<?php
class Common extends Services
{
    public static function test() {
        echo 'this is test';
    }

    /**
     * !common:mailer
     */
    public static function mailer() {
        Process::load('Mailer');
    }
}
