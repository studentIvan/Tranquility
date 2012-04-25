<?php
class Security
{
    /**
     * @var string
     */
    protected static $secret = null;

    /**
     * @static
     * @param string $secret
     * @throw Exception
     */
    public static function setSecret($secret)
    {
        if (is_null(self::$secret)) {
            self::$secret = $secret;
        } else {
            throw new Exception('Too many security_token changes');
        }
    }

    /**
     * @static
     * @param string|int|array $var
     * @return string
     */
    public static function getDigest($var)
    {
        $buffer = '';

        if (is_array($var)) {
            foreach ($var as $element) $buffer .= self::getDigest($element);
        } else {
            $buffer = md5((($var) ? $var : '0'));
        }

        return md5($buffer . self::$secret);
    }

    public static function getCsrfToken()
    {
        return self::getDigest(Session::getToken());
    }
}
