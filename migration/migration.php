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

        //TODO fix this
        //if($dbConfig->getMigrationInProgress()){
            //TODO safeguard if migration fails
            /*$date = new \DateTime($dbConfig->getValue('migration_last_started'));
            if($date->add(new \DateInterval('PT' . $dbConfig->getMigrationAllowedDuration())) < new \DateTime())
                $dbConfig->setMigrationInProgress(false);
            else*/
                //die('Databse migration in progress... Please try again in a few moments!');
        //}

        //$dbConfig->setMigrationInProgress(true);
        //TODO store migration time in db

        $dbVersion = $dbConfig->getMigrationVersion();

        $current = $dbVersion;
        $didMigrate = false;
        try{
            while($current = $this->findNextMigrationFile($current, $migrationLimit)) {
                $didMigrate = true;
                $this->runMigration($current);
            }
        } catch (\Exception $e){
            die($e->getMessage());
        }

        //$dbConfig->setMigrationInProgress(false);

        /*if($didMigrate)
            echo "<hr>Migrating done, please reload the page!<hr>";*/

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
        /*echo '<hr>RUNNING MIGRATION '.$migrationNum . '<hr>';*/

        $migFile =  $this->getMigrationFile($migrationNum);
        $migClass = $this->getMigrationClass($migrationNum);

        include $migFile;

        /** @var migrationAbstract $m */
        $m = new $migClass();

        if(!is_a($m, '\maidea\migration\migrationAbstract')) {       //TODO can remove namespace?
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