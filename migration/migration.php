<?php

namespace maidea\migration;

class migration
{

    const MIGRATION_DIR = __DIR__;

    private $pdo;

    public function __construct()
    {
        $this->pdo = \maidea\db::getPdoHandle();
    }

    public function doUpgrades()
    {

        $config = \maidea\config::getConfig();
        $migrationLimit = $config['db']['migrationVersion'];

        $dbConfig = new \maidea\model\configs();

        if($dbConfig->getMigrationInProgress()){
            if($dbConfig->getMigrationLastStarted()->add(new DateInterval('PT' . $dbConfig->getMigrationAllowedDuration())) < new \DateTime())
                $dbConfig->setValue('migration_in_progress', '0');
            else
                die('Databse migration in progress... Please try again in a few moments!');
        }

        $dbVersion = $dbConfig->getMigrationVersion();

        $current = $dbVersion;
        try{
            while($current = $this->findNextMigrationFile($current, $migrationLimit))
                $this->runMigration($current);
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
        echo '<hr>RUNNING MIGRATION '.$migrationNum . '<hr>';

        $migFile =  $this->getMigrationFile($migrationNum);
        $migClass = $this->getMigrationClass($migrationNum);

        include $migFile;

        /** @var migrationAbstract $m */
        $m = new $migClass();

        if(!is_a($m, '\maidea\migration\migrationAbstract')) {       //TODO can remove namespace?
            echo 'EXCEPTIOJ';
            throw new \Exception('Migration does not extend migrationAbstract class!');
        }


        $m->upgrade();


        $this->markAsMigrated($migrationNum);

    }

    private function markAsMigrated($migration)
    {
        $configs = new \maidea\model\configs();
        $configs->setMigrationVersion($migration);
    }

    private function getMigrationFile($migrationNum)
    {
        return self::MIGRATION_DIR . DIRECTORY_SEPARATOR . $migrationNum . '.php';
    }

    private function getMigrationClass($migrationNum)
    {
        return '\maidea\migration\migration_' . $migrationNum;      //TODO can remove namespace?
    }


}