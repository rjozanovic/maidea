<?php


namespace maidea\migration;

class migration_5 extends migrationAbstract
{
    public function upgrade()
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS favorite (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, cookie VARCHAR(128) NOT NULL, city_id INT NOT NULL) ENGINE=INNODB;");
    }
}


