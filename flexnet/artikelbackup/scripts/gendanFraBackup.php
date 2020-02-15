<?php
include("./script-constants.inc.php");

if(!isset($db)) {
    $db = new db(DB_NAME);
}

$showDebug = false;
$devMsgs = [];

$backup_id = isset($_POST["backup_id"]) ? intval($_POST["backup_id"]) : 0;

// Artikel skal være backed op inden vi når hertil
// Her overskrives artiklen i artikler-tabellen

sendNoCacheHeaders();

if(!$backup_id) {
    returnJson([
        "error" => "backup_id ikke angivet"
    ]);
}

$backup = $db->get_row(
    $backup_tabel,
    $backup_id
);
$devMsgs[] = $db->sql;

if(empty($backup)) {
    returnJson([
        "error" => "Backup {$backup_id} ikke fundet"
    ]);
}

// Indlæs backup
$backup["edited_at"] = date(DATOFORMAT);
$db->update(
    $artikel_tabel,
    $backup["artikel_id"],
    $backup
);
$devMsgs[] = $db->sql;

$db->insert("log", [
    "objekt_id"	=> $backup["artikel_id"],
    "tabel"		=> $artikel_tabel,
    "action"	=> "backup indlæst",
    "bruger_id"	=> isset($_SESSION["login_id"]) ? $_SESSION["login_id"] : 0,
    "note"      => "Backup indlæst fra id {$backup_id}"
]);
$devMsgs[] = $db->sql;


// Slet backup (er allerede gemt i seneste udgave)
$db->delete(
    $backup_tabel,
    $backup_id
);
$devMsgs[] = $db->sql;

foreach($_POST as $key=>$val) {
    $devMsgs[] = $key;
}

if($showDebug) {
    array_walk($devMsgs, "rens_query");
}
returnJson([
    "success"   => true,
    "result"    => "Artikel gendannet fra backup {$backup_id}",
    "debug"     => $showDebug ? $devMsgs : null
]);