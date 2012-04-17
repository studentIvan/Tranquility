<?php
/**
 * Singleton Database
 *
 * Usage: $pdo = Database::getInstance();
 */
class Database
{
    /**
     * @var PDO
     */
    protected static $instance = null;

    protected static $dsn, $username, $password;

    /**
     * @static
     * @return PDO
     * @throws Exception
     */
    public static function getInstance()
    {
        if (is_null(self::$dsn)) {
            throw new Exception('Database is not configured');
        }

        if (is_null(self::$instance)) {
            self::$instance = new PDO(self::$dsn, self::$username, self::$password);
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$instance;
    }

    /**
     * @static
     * @param string $dsn
     * @param string $username
     * @param string $password
     */
    public static function setConfiguration($dsn, $username, $password)
    {
        self::$dsn = $dsn;
        self::$username = $username;
        self::$password = $password;
    }

    /**
     * @static
     * @param string $sql
     * @return string First result
     */
    public static function getSingleResult($sql)
    {
        $result = self::getInstance()->query($sql)->fetch(PDO::FETCH_NUM);
        return $result[0];
    }

    /**
     * @static
     * @param string $from
     * @return int
     */
    public static function count($from)
    {
        return intval(self::getSingleResult("SELECT COUNT(*) FROM $from"));
    }

    /**
     * @static
     * @return string
     */
    public static function getDateTimeNow()
    {
        return date('Y-m-d H:i:sP');
    }
}
