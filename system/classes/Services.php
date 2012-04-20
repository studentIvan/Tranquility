<?php
abstract class Services
{
    public static function session()
    {
        try {
            Session::start();
        } catch (SessionException $e) {
            if ($e->getCode() == 403 or $e->getCode() == 404) throw $e;
        }
    }
}
