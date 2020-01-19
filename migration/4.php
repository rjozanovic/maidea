<?php

namespace maidea\migration;

class migration_4 extends migrationAbstract
{
    public function upgrade()
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS forecast (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, city_id INT NOT NULL, datetime DATETIME NOT NULL, json TEXT NOT NULL) ENGINE=INNODB;");
        //TODO pull data for base cities
    }
}

