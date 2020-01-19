<?php

/**
 * Class migration_1
 *
 * Initial migration, creates and populates config table.
 *
 */

namespace maidea\migration;

class migration_1 extends migrationAbstract
{
    public function upgrade()
    {
        try{
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS config ( id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL UNIQUE, value VARCHAR(255) NOT NULL ) ENGINE=INNODB; ");
            $this->pdo->exec("INSERT INTO config (name, value) VALUES ('migration_version', '0');");
            $this->pdo->exec("INSERT INTO config (name, value) VALUES ('migration_in_progress', '0');");
            $this->pdo->exec("INSERT INTO config (name, value) VALUES ('migration_last_started', '0');");
            $this->pdo->exec("INSERT INTO config (name, value) VALUES ('migration_allowed_duration', '600');");

        } catch(\Exception $e){
            die($e->getMessage());
        }


    }
}
