

<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//phpinfo();

function autoloader($class)
{
    $pieces = explode('\\', $class);
    if($pieces[0] === 'maidea'){
        array_shift($pieces);
        if($pieces[0] === 'migration' && isset($pieces[1])){  //migration files
            if(preg_match('~migration_([0-9]+)~', $pieces[1], $matches))
                $pieces[1] = $matches[1];
        }
        $filename = __DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $pieces) . '.php';
        require_once $filename;
    }
}

spl_autoload_register('autoloader');


/*include "migration/migration.php";
include "migration/abstract.php";
include "config.php";
include "db.php";
*/


$pdo = maidea\db::getPdoHandle();

$mig = new maidea\migration\migration($pdo);

$mig->doUpgrades();


