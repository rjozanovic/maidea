<?php

namespace maidea;

class db
{
    private static $pdo = null;

    private function __construct(){}

    /**
     * @return /PDO
     */
    public static function getPdoHandle()
    {
        self::connect();
        return self::$pdo;
    }

    private static function connect()
    {
        if(!self::$pdo){
            try{
                $config = \maidea\config::getConfig();
                $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['dbname'];
                self::$pdo = new \PDO($dsn, $config['db']['username'], $config['db']['password']);

            } catch (Exception $e){
                die('ERR connecting to db: ' . $e->getMessage());
            }
        }
    }

    public static function reconnect()
    {
        self::$pdo = null;
        self::connect();
    }

}
