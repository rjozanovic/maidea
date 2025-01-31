
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!isset($_COOKIE['maidea_weather']))
    setcookie("maidea_weather", uniqid(), time() + 3600 * 24 * 365);

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

$mig = new \maidea\migration\migration();
$mig->doUpgrades();

$controller = new \maidea\controller\indexController();

