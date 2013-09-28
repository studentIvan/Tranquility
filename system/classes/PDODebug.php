<?php
class PDODebug extends PDO 
{
    public static $queriesCount = 0;

    public function query($data) {
        self::$queriesCount++;
        return parent::query($data);
    }

    public function execute($data = null) {
        self::$queriesCount++;
        return parent::execute($data);
    }
}