<?php

namespace maidea\migration;

class migration
{

    const MIGRATION_DIR = __DIR__;

    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function doUpgrades()
    {

        //echo 'Doint upgrades.<hr>';

        $migrationLimit = \maidea\config::MIGRATE_VERSION;

        $current = 0;

        try{
            while($current = $this->findNextMigrationFile($current, $migrationLimit)){
                //echo '<br>' . $current . '</br>';
                $this->runMigration($current);
            }
        } catch (\Exception $e){
            die($e->getMessage());
        }

    }

    private function findNextMigrationFile($currentDbVersion, $migrationLimit)
    {
        if ($h = opendir(self::MIGRATION_DIR)) {
            $bestCandidate = null;
            while (false !== ($fileName = readdir($h))) {

                $filePath = self::MIGRATION_DIR . DIRECTORY_SEPARATOR . $fileName;

                if('.' === $fileName) continue;
                if('..' === $fileName) continue;
                if(is_dir($filePath)) continue;

                $path_parts = pathinfo($filePath);
                $currentVersion = $path_parts['filename'];

                //echo $currentVersion . '-' . (int)($currentVersion <= $migrationLimit) . '-' . (int)($currentVersion > $currentDbVersion) . '-' . (int)($bestCandidate === null || $currentVersion < $bestCandidate) . '<br>';

                if(is_numeric($currentVersion) && $currentVersion <= $migrationLimit && $currentVersion > $currentDbVersion && ($bestCandidate === null || $currentVersion < $bestCandidate)){      //TODO via regex or check extension
                    $bestCandidate = $currentVersion;
                    //echo 'BEST = ' . $bestCandidate . ', ';
                }
            }
            closedir($h);
            //echo 'RETURNING: '. $bestCandidate;
            return $bestCandidate;
        } else
            throw new \Exception('Can\'t open migration directory!');
    }

    private function runMigration($migrationNum)
    {
        echo 'RUNNING '.$migrationNum;

        $migFile =  $this->getMigrationFile($migrationNum);
        $migClass = $this->getMigrationClass($migrationNum);

        include $migFile;

        /** @var migrationAbstract $m */
        $m = new $migClass($this->pdo);

        if(!is_a($m, 'maidea\migration\migrationAbstract')) {
            echo 'EXCEPTIOJ';
            throw new \Exception('Migration does not extend migrationAbstract class!');
        }

        echo 'upgrading ' . $migrationNum;

        $m->upgrade();

        echo 'About to run migration: ' . $migrationNum;

        $this->markAsMigrated($migrationNum);

    }

    private function markAsMigrated($migration)
    {
        //TODO
    }

    private function getMigrationFile($migrationNum)
    {
        return self::MIGRATION_DIR . DIRECTORY_SEPARATOR . $migrationNum . '.php';
    }

    private function getMigrationClass($migrationNum)
    {
        return 'maidea\migration\migration_' . $migrationNum;
    }


}