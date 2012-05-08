<?php
class ULogin
{
    /**
     * @static
     * @param string $token
     * @return array
     */
    public static function getData($token) {
        $s = file_get_contents('http://ulogin.ru/token.php?token=' . $token . '&host=' . $_SERVER['HTTP_HOST']);
        return json_decode($s, true);
    }

    /**
     * @static
     *
     */
    public static function initSession() {
        //Process::$context['user'] = Session::getAllStorageData();
    }

    /**
     * @static
     * @return bool
     */
    public static function authorize()
    {
        if ($token = Data::input('token')) {
            $userData = self::getData($token);
            if (isset($userData['first_name']) and isset($userData['last_name'])) {
                /*foreach ($userData as $key => $value) {
                    Session::setStorageData($key, $value);
                }*/
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
