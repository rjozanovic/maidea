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
        echo 'Upgrading 1';

        //TODO
        //create database
        //create user with perm on database
        //modify conf to use new db connection stuff
        //create conf table
        //initialise conf variables (migration version!!!)



        /*$this->pdo->exec("CREATE DATABASE IF NOT EXISTS maidea_rudolf_jozanovic;");

        $this->pdo->exec("CREATE USER IF NOT EXISTS 'maidea_rj'@localhost IDENTIFIED BY 'password';");
        $this->pdo->exec("GRANT ALL PRIVILEGES ON maidea_rudolf_jozanovic.* TO maidea_rj@localhost;");
        $this->pdo->exec("CREATE TABLE config ( id INT AUTO_INCREMENT PRIMARY KEY, key VARCHAR(64) NOT NULL, value INT NOT NULL ) ENGINE=INNODB; ");

        $this->pdo->exec("INSERT INTO config (key, value) VALUES ('migration_version', 0);");
        $this->pdo->exec("INSERT INTO config (key, value) VALUES ('migration_in_progress', 0);");
        $this->pdo->exec("INSERT INTO config (key, value) VALUES ('migration_last_started', 0);");*/


    }
}
