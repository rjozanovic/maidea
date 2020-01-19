<?php

namespace maidea\migration;


class migration_2 extends migrationAbstract
{
    public function upgrade()
    {
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS city (id INT UNSIGNED PRIMARY KEY, name VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, data TEXT NOT NULL) ENGINE=INNODB;');
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS weather (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, city_id INT NOT NULL, datetime DATETIME NOT NULL, data TEXT NOT NULL) ENGINE=INNODB;");

        $this->pdo->exec("INSERT INTO config (name, value) VALUES ('weather_valid_duration', '3600');");

        //TODO pull city data
        exec("doTask.php $arg1 $arg2 $arg3 >/dev/null 2>&1 &");

    }
}
