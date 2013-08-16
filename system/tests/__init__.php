<?php
require_once dirname(__FILE__) . '/../__init__.php';

class SecurityDebug extends Security
{
    public static function getDigestWithSpecialSecret($var, $secret)
    {
        self::$secret = $secret;
        return self::getDigest($var);
    }
}