

<?php

//phpinfo();

include "migration/migration.php";
include "migration/abstract.php";
include "config.php";
include "db.php";

$db = new db();
$pdo = $db->getPdoHandle();

$mig = new Migration($pdo);

$mig->doUpgrades();
