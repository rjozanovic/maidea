<?php

namespace maidea\migration;

class migration_3 extends migrationAbstract
{
    public function upgrade()
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS weather (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, city_id INT NOT NULL, datetime DATETIME NOT NULL, json TEXT NOT NULL) ENGINE=INNODB;");
        $this->pdo->exec("INSERT INTO config (name, value) VALUES ('weather_valid_duration', '3600');");
        //TODO pull data for base cities
    }
}

