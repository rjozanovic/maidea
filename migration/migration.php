<?php

class Migration
{

    const MIGRATION_DIR = __DIR__ . DIRECTORY_SEPARATOR . 'migrations';

    public function doUpgrades()
    {
        $migrationLimit = config::MIGRATE_VERSION;
    }

    private function findNextMigrationFile($currentDbVersion, $migrationLimit)
    {

        if ($h = opendir(self::MIGRATION_DIR)) {
            while (false !== ($file = readdir($h))) {
                if('.' === $file) continue;
                if('..' === $file) continue;
                if(is_dir($file)) continue;
                echo $file;
            }
            closedir($h);
        }
    }

}