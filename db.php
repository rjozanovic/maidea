<?php

class db
{
    private $pdo = null;

    public function getPdoHandle()
    {
        $this->connect();
        return $this->pdo;
    }

    private function connect()
    {
        if(!$this->pdo){
            try{
                $user = config::DB_USER_NAME;
                $pass = config::DB_USER_PASSWORD;
                $host = config::DB_HOST;
                $db = config::DB_DATABASE_NAME;

                $dsn = 'mysql:host=' . $host;
                $dsn .= $db ? ';dbname=' . $db : '';

                echo 'conn string: ' . $dsn;

                $this->pdo = new PDO($dsn, $user, $pass);

            } catch (Exception $e){
                die('ERR connecting to db: ' . $e->getMessage());
            }
        }
    }

    public function reconnect()
    {
        $this->pdo = null;
        $this->connect();
    }

}
