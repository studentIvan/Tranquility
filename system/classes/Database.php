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
}
