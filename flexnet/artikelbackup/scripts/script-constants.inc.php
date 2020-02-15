<?php
$grundsti = "/flexnet/artikelbackup";
$artikel_tabel = "artikler";
$backup_tabel = "artikler_backup";
define(
    "ABSPATH",
    str_replace(
        "{$grundsti}/scripts", 
        "", 
        realpath("")
    )."/"
);

include(ABSPATH."includes/constants.inc.php");
include(ABSPATH."includes/functions.inc.php");
include(ABSPATH."classes/class.pdo.php");


