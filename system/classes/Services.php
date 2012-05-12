<?php
abstract class Services
{
    public static function session()
    {
        try {
            Session::start();
            Process::$context['csrf_token'] = Security::getCsrfToken();
            if (function_exists('afterSessionStartedCallback')) {
                afterSessionStartedCallback();
            }
        } catch (SessionException $e) {
            if ($e->getCode() == 403 or $e->getCode() == 404) throw $e;
        }
    }
}
