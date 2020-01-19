<?php

if(count($argv) >= 2) {       //background task

    $_REQUEST['controller'] = 'backend';
    $_REQUEST['action'] = $argv[1];
    $_REQUEST['consoleParams'] = array_slice($argv, 2);

    foreach($_REQUEST['consoleParams'] as $paramPair){
        $p = explode('=', $paramPair);
        if(count($p) === 2)
            $_REQUEST[$p[0]] = $p[1];
    }

    include "index.php";

}
