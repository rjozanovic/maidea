<?php

namespace maidea;

class db
{
    private static $pdo = null;

    private function __construct(){}

    public static function getPdoHandle()
    {
        self::connect();
        return self::$pdo;
    }

    private static function connect()
    {
        if(!self::$pdo){
            try{
                $user = config::DB_USER_NAME;
                $pass = config::DB_USER_PASSWORD;
                $host = config::DB_HOST;
                $db = config::DB_DATABASE_NAME;

                $dsn = 'mysql:host=' . $host;
                $dsn .= $db ? ';dbname=' . $db : '';

                echo 'conn string: ' . $dsn;

                self::$pdo = new \PDO($dsn, $user, $pass);

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
