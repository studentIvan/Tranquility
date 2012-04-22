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
        $options = array(),
        $token = '',
        $started = false;

    public static function start()
    {
        if (self::$started)
            throw new SessionException('Session already started');

        if (isset(self::$options['garbage_auto_dump']) and
            self::$options['garbage_auto_dump'] and (intval(date('i'))%2 == 0)) {
            self::dumpGarbage();
        }

        list($token, $uid, $pass, $signature) = Cookies::get(array('t', 'u', 'p', 's'));

        if ($token and $signature == Security::getDigest(array($token, $uid, $pass)))
        {
            $pdo = Database::getInstance();
            $pdo->query("UPDATE sessions SET uptime=NOW() WHERE token='$token'");
            $result = $pdo->query("SELECT role FROM sessions WHERE token='$token'");
            if ($result->rowCount() == 0) {
                self::restore($token, $uid, $pass);
            } else {
                self::$token = $token;
                self::$uid = intval($uid);
                self::$role = intval($result->fetchColumn());
            }
        }
        else
        {
            self::create();
        }

        self::$started = true;
    }

    public static function dumpGarbage()
    {
        $lifetime = isset(self::$options['lifetime_hours']) ? self::$options['lifetime_hours'] : 1;
        Database::getInstance()->query("DELETE FROM sessions WHERE uptime < ( NOW() - INTERVAL $lifetime HOUR )");
    }

    public static function isAuth()
    {
        return (self::$uid !== 0);
    }

    protected static function restore($token, $uid, $pass)
    {
        if (!self::authenticateByUserId($uid, $pass))
            $token = $uid = $pass = null;

        self::create($token, $uid, $pass);
    }

    protected static function create($token = null, $uid = null, $pass = null)
    {
        $pdo = Database::getInstance();
        $ip = $_SERVER['REMOTE_ADDR'];
        $agent = substr(htmlspecialchars(trim($_SERVER['HTTP_USER_AGENT']), ENT_QUOTES), 0, 110);

        if (self::isBot($agent))
        {
            $token = (is_null($token)) ? Security::getDigest(array($ip, $agent)) : $token;
            $role = self::$options['bot_role'];

            if (Database::count("sessions WHERE token='$token'") !== 0)
            {
                $pdo->query("UPDATE sessions SET uptime=NOW() WHERE token='$token'");
            }
            else
            {
                $sql = "INSERT INTO sessions (`token`, `uid`, `role`, `ip`, `useragent`, `uptime`)
                  VALUES (:token, 0, :role, INET_ATON(:ip), :agent, NOW())";

                $statement = $pdo->prepare($sql);
                $statement->bindParam(':token', $token, PDO::PARAM_STR);
                $statement->bindParam(':role', $role, PDO::PARAM_INT);
                $statement->bindParam(':ip', $ip, PDO::PARAM_STR);
                $statement->bindParam(':agent', $agent, PDO::PARAM_STR);

                if (!$statement->execute())
                    throw new SessionException('Database error', 403);
            }

            self::$role = $role;
        }
        else
        {
            if (Database::count("sessions WHERE ip=INET_ATON('$ip')") > 10)
                throw new SessionException('Too many connections', 403);

            $token = (is_null($token)) ? Security::getDigest(array($ip, $agent, rand(1000, 9999))) : $token;

            $role = (is_null($uid)) ? self::$options['guest_role'] :
                Database::getSingleResult("SELECT role FROM users WHERE id='$uid'");

            self::$role = $role;
            $usid = (is_null($uid)) ? '0' : $uid;

            $sql = "INSERT INTO sessions (`token`, `uid`, `role`, `ip`, `useragent`, `uptime`)
                  VALUES (:token, :uid, :role, INET_ATON(:ip), :agent, NOW())";

            $statement = $pdo->prepare($sql);
            $statement->bindParam(':token', $token, PDO::PARAM_STR);
            $statement->bindParam(':uid', $usid, PDO::PARAM_INT);
            $statement->bindParam(':role', $role, PDO::PARAM_INT);
            $statement->bindParam(':ip', $ip, PDO::PARAM_STR);
            $statement->bindParam(':agent', $agent, PDO::PARAM_STR);

            if (!$statement->execute())
                throw new SessionException('Database error', 403);
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

        /**
         * Referer register
         */
        if (isset(self::$options['referers']) and self::$options['referers']
            and isset($_SERVER['HTTP_REFERER']) and !empty($_SERVER['HTTP_REFERER'])
            and filter_var($_SERVER['HTTP_REFERER'], FILTER_VALIDATE_URL))
        {
            $ref = substr(str_replace(array('<', '>'), '', $_SERVER['HTTP_REFERER']), 0, 200);
            $statement = $pdo->prepare("INSERT INTO referers (timepoint, token, url) VALUES (NOW(), :token, :url)");
            $statement->bindParam(':token', $token, PDO::PARAM_STR);
            $statement->bindParam(':url', $ref, PDO::PARAM_STR);
            $statement->execute();
        }
    }

    public static function setConfiguration($configuration)
    {
        self::$options = $configuration;
    }

    protected static function isBot($agent)
    {
        $bots = array('yandex', 'yadirect', 'google', 'rambler', 'yahoo', 'msn', 'alexa', 'archiver', 'dotnet');
        $block = array('xrumer', 'xpymep', 'xspider');

        foreach ($block as $b)
        {
            if (stripos($agent, $b) !== false) {
                throw new Exception('fucking bot', 403);
            }
        }

        foreach ($bots as $bot)
        {
            if (stripos($agent, $bot) !== false) {
                return true;
            }
        }

        return false;
    }

    protected static function authenticateByUserId($uid, $pass)
    {
        return (Database::count("users WHERE id='$uid' AND password='$pass'") !== 0);
    }

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
     */
    public static function authorize($login, $password, $temporary = false, $authenticate = true)
    {
        if (self::isAuth())
            throw new AuthException('You are logged in');

        $password = Security::getDigest($password);

        if ($authenticate and !self::authenticateByLogin($login, $password))
            throw new AuthException('Authenticate failed');

        if (self::$started)
        {
            $token = self::$token;
            $pdo = Database::getInstance();
            $user = $pdo->query("SELECT id, role FROM users WHERE login='$login'")->fetch(PDO::FETCH_OBJ);
            $pdo->query("UPDATE sessions SET uid='{$user->id}', role='{$user->role}' WHERE token='$token'");
            self::$uid = intval($user->id);
            self::$role = $user->role;

            Cookies::set(

                array(
                    't' => $token,
                    'u' => $user->id,
                    'p' => $password,
                    's' => Security::getDigest(array($token, $user->id, $password)),
                ),

                null, (($temporary) ? 0 : false)
            );
        }
        else
        {
            throw new SessionException('Session not started');
        }
    }

    public static function stop()
    {
        if (!self::$started)
            throw new SessionException('Session not started');

        $token = self::$token;
        self::$token = '';
        self::$uid = 0;
        self::$started = false;

        Cookies::set(array('t' => '', 'u' => '', 'p' => '', 's' => ''));
        Database::getInstance()->query("DELETE FROM sessions WHERE token='$token'");
    }

    public static function getUid()
    {
        return intval(self::$uid);
    }

    public static function getRole()
    {
        return intval(self::$role);
    }

    public static function getToken()
    {
        return self::$token;
    }

    /**
     * @static
     * @param int $offset
     * @param bool|int $limit
     * @return array
     */
    public static function getAll($offset = 0, $limit = 30)
    {
        $statement = Database::getInstance()->prepare("
            SELECT s.token AS token, u.login AS user,
            INET_NTOA(s.ip) AS ip, s.useragent AS user_agent,
            s.uptime AS uptime, r.title AS session_role, f.url AS referer
            FROM sessions s
            LEFT JOIN users u ON s.uid=u.id
            LEFT JOIN roles r ON r.id=s.role
            LEFT JOIN referers f ON f.token=s.token
            ORDER BY s.uptime DESC
            LIMIT :limit OFFSET :offset
        ");

        $statement->bindParam(':limit', $limit, PDO::PARAM_INT);
        $statement->bindParam(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
