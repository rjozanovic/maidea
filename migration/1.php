<?php

/**
 * Class migration_1
 *
 * Initial migration, creates database and sets up for further migrations.
 *
 */

namespace maidea\migration;

class migration_1 extends migrationAbstract
{
    public function upgrade()
    {

        try{
            /*$this->pdo->exec("CREATE DATABASE IF NOT EXISTS maidea_rudolf_jozanovic;");

            $this->pdo->exec("CREATE USER IF NOT EXISTS 'maidea_rj'@localhost IDENTIFIED BY 'password';");
            $this->pdo->exec("GRANT ALL PRIVILEGES ON maidea_rudolf_jozanovic.* TO maidea_rj@localhost;");

            $this->pdo->exec("USE maidea_rudolf_jozanovic;");*/

            $this->pdo->exec("CREATE TABLE IF NOT EXISTS config ( name VARCHAR(255) PRIMARY KEY, value VARCHAR(255) NOT NULL ) ENGINE=INNODB; ");

            $this->pdo->exec("INSERT INTO config (name, value) VALUES ('migration_version', '0');");
            $this->pdo->exec("INSERT INTO config (name, value) VALUES ('migration_in_progress', '0');");
            $this->pdo->exec("INSERT INTO config (name, value) VALUES ('migration_last_started', '0');");
            $this->pdo->exec("INSERT INTO config (name, value) VALUES ('migration_allowed_duration', '600');");

        } catch(\Exception $e){
            die($e->getMessage());
        }


    }
}
