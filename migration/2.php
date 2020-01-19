<?php

/**
 * Creates city table, downloads a list of cities and populates table.
 *
 * TODO make in progress set to 0 only after background process is done
 *
 */

namespace maidea\migration;


class migration_2 extends migrationAbstract
{
    public function upgrade()
    {
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS city (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, city_id INT NOT NULL, name VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL) ENGINE=INNODB;');
        exec("php backgroundTask.php pullCityData >/dev/null 2>&1 &");
    }
}
