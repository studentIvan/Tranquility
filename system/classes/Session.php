<?php
/**
 * Session Helper
 *
 * Cookies:
 * t - session token
 * u - user id (for authenticate)
 * p - password digest (for authenticate)
 * s - digest signature (for security)
 *
 * blank = 0 (zero)
 */
class Session
{
    protected static
        $uid = 0,
        $role = 0,
        $defaultRole = 4,
        $token = '',
        $started = false;

    /**
     * @static
     *
     */
    public static function start()
    {
        if (self::$started) return;
        list($token, $uid, $pass, $signature) = Cookies::get(array('t', 'u', 'p', 's'));

        if ($token and $signature == Security::getDigest(array($token, $uid, $pass)))
        {
            $pdo = Database::getInstance();
            $now = Database::getDateTimeNow();
            $result = $pdo->exec("UPDATE sessions SET uptime='$now' WHERE token='$token'");
            if ($result == 0 and Database::count("sessions WHERE token='$token'") == 0) {
                self::restore($token, $uid, $pass);
            } else {
                self::$token = $token;
                self::$uid = $uid;
            }
        }
        else
        {
            self::create();
        }

        self::$started = true;
    }

    /**
     * @static
     * @param string $token
     * @param int $uid
     * @param string $pass
     */
    protected static function restore($token, $uid, $pass)
    {
        if (!self::authenticateByUserId($uid, $pass))
            $token = $uid = $pass = null;

        self::create($token, $uid, $pass);
    }

    /**
     * @static
     * @throws Exception
     * @param null|string $token
     * @param null|int $uid
     * @param null|string $pass
     */
    protected static function create($token = null, $uid = null, $pass = null)
    {
        $pdo = Database::getInstance();
        $ip = $_SERVER['REMOTE_ADDR'];
        $agent = substr(htmlspecialchars(trim($_SERVER['HTTP_USER_AGENT']), ENT_QUOTES), 0, 50);

        if (self::isBot($agent))
        {
            $token = (is_null($token)) ? Security::getDigest(array($ip, $agent)) : $token;

            if (Database::count("sessions WHERE token='$token'") !== 0)
            {
                $now = Database::getDateTimeNow();
                $pdo->query("UPDATE sessions SET uptime='$now' WHERE token='$token'");
            }

            self::$role = self::$defaultRole;
        }
        else
        {
            if (Database::count("sessions WHERE ip=INET_ATON('$ip')") > 10)
                throw new Exception('Too many connections', 403);

            $token = (is_null($token)) ? Security::getDigest(array($ip, $agent, rand(1000, 9999))) : $token;

            $role = (is_null($uid)) ? self::$defaultRole :
                Database::getSingleResult("SELECT role FROM users WHERE id='$uid'");

            self::$role = $role;
            $now = Database::getDateTimeNow();
            $usid = (is_null($uid)) ? '0' : $uid;

            $pdo->query(
                "INSERT INTO sessions (`token`, `uid`, `role`, `ip`, `useragent`, `uptime`)
                  VALUES ('$token', '$usid', '$role', INET_ATON('$ip'), '$agent', '$now')"
            );
        }

        self::$token = $token;

        $uid = (is_null($uid)) ? '0' : $uid;
        $pass = (is_null($pass)) ? '0' : $pass;

        Cookies::set(array(
            't' => $token,
            'u' => $uid,
            'p' => $pass,
            's' => Security::getDigest(
                array($token, $uid, $pass)
            ),
        ));
    }

    /**
     * @static
     * @param bool|int $role
     */
    public static function setDefaultRole($role = false)
    {
        self::$defaultRole = ($role) ? intval($role) : self::$defaultRole;
    }

    /**
     * @static
     * @param string $agent
     * @return bool
     */
    protected static function isBot($agent)
    {
        return false;
    }

    /**
     * @static
     * @param int $uid
     * @param string $pass
     * @return bool
     */
    protected static function authenticateByUserId($uid, $pass)
    {
        return (Database::count("users WHERE id='$uid' AND password='$pass'") !== 0);
    }

    /**
     * @static
     * @param string $login
     * @param string $pass
     * @return bool
     */
    protected static function authenticateByLogin($login, $pass)
    {
        return (Database::count("users WHERE login='$login' AND password='$pass'") !== 0);
    }

    /**
     * @static
     * @param string $login
     * @param string $password
     * @param bool $temporary
     * @param bool $authenticate
     * @return bool
     */
    public static function authorize($login, $password, $temporary = false, $authenticate = true)
    {
        $password = Security::getDigest($password);
        if (self::$started and (!$authenticate or ($authenticate and self::authenticateByLogin($login, $password))))
        {
            $token = self::$token;
            $pdo = Database::getInstance();
            $user = $pdo->query("SELECT id, role FROM users WHERE login='$login'")->fetch(PDO::FETCH_OBJ);
            $pdo->query("UPDATE sessions SET uid='{$user->id}', role='{$user->role}' WHERE token='$token'");
            self::$uid = $user->id;
            self::$role = $user->role;

            Cookies::set(

                array(
                    't' => $token,
                    'u' => $user->id,
                    'p' => $password,
                    's' => Security::getDigest(array($token, $user->id, $password)),
                ),

                null, (($temporary) ? 1 : false)
            );

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * @static
     * @return mixed
     */
    public static function stop()
    {
        if (!self::$started) return;

        $token = self::$token;
        self::$token = '';
        self::$uid = 0;
        self::$started = false;

        Cookies::set(array('t' => '', 'u' => '', 'p' => '', 's' => ''));
        Database::getInstance()->query("DELETE FROM sessions WHERE token='$token'");
    }

    /**
     * @static
     * @return int
     */
    public static function getUid()
    {
        return self::$uid;
    }

    /**
     * @static
     * @return int
     */
    public static function getRole()
    {
        return self::$role;
    }

    /**
     * @static
     * @return string
     */
    public static function getToken()
    {
        return self::$token;
    }
}
