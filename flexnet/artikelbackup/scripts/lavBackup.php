<?php
include("./script-constants.inc.php");

$artikel_id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;

// Ved gendannelse tages der en backup først. sørg for ikke at slette den udgave der skal gendannes.
$slet_ikke_id = isset($_POST["slet_ikke_id"]) 
    ? intval($_POST["slet_ikke_id"]) 
    : 0
;

// Automatisk gentagen backup af artikel (ved ændringer)

sendNoCacheHeaders();

if(!$artikel_id) {
    returnjson([
        "error" => "artikel_id ikke angivet"
    ]);
}

$showDebug = false;
$devMsgs = [];

$data = $_POST;
$data["artikel_id"] = $artikel_id;

/* ---- PUBLICERING ---- */
if (isset($data['publiceringDato']) && $data['publiceringDato']) {
    $tid = rens_post_tid($data["publiceringTimer"], $data["publiceringMinutter"]);
    swap_dato($data["publiceringDato"], "dkus") . " {$tid}";
} else {
    $data["publicering"] = date(DATOFORMAT);
}
/* ---- PUBLICERING SLUT ---- */

/* Ritzau hack */
$data["broed"] = str_replace('"/livewire', '"https://livewire', $data["broed"]);
$data["broed"] = str_replace('"livewire', '"https://livewire', $data["broed"]);
$data["broed_tekst"] = strip_tags($data['broed']);
/* Hent slug */
$data["slug"] = $db->get_value(
    $artikel_tabel,
    $artikel_id,
    "slug"
);

$eksisterendeBackupIds = $db->get_ids(
    $backup_tabel,
    "id DESC",
    "artikel_id = {$artikel_id} AND id <> ${slet_ikke_id}"
);
$devMsgs[] = $db->sql;

if(count($eksisterendeBackupIds) > 4) {
    $sletIds = array_slice($eksisterendeBackupIds, 4);
    $db->delete(
        $backup_tabel,
        0,
        "id IN (".implode(", ", $sletIds).")"
    );
    $devMsgs[] = $db->sql;
}

$data["created_at"] = date(DATOFORMAT);
$db->insert(
    $backup_tabel,
    $data
);
$devMsgs[] = $db->sql;
$backup_id = $db->lastid();

$db->insert("log", [
    "objekt_id"	=> $artikel_id,
    "tabel"		=> $artikel_tabel,
    "action"	=> "backup",
    "bruger_id"	=> isset($_SESSION["login_id"]) ? $_SESSION["login_id"] : 0,
    "note"      => "Backup id {$backup_id}"
]);
$devMsgs[] = $db->sql;

if($showDebug) {
    array_walk($devMsgs, "rens_query");
}
returnJson([
    "success"   => true,
    "result"    => "Artikel {$artikel_id} - backup {$backup_id} gemt",
    "debug"     => $showDebug ? $devMsgs : null
]);


if(!function_exists("rens_post_tid")) {
    function rens_post_tid($timer, $minutter, $sekunder = "0") {
        $timer = intval($timer);
        $minutter = intval($minutter);
        if($timer > 23) {
            $timer = 23;
        } else if($timer < 0) {
            $timer = 0;
        }
        if($minutter > 59) {
            $minutter = 59;
        } else if($minutter < 0) {
            $minutter = 0;
        }
        if($sekunder > 59) {
            $sekunder = 59;
        } else if($sekunder < 0) {
            $sekunder = 0;
        }
        $timer = sprintf("%02d", $timer);
        $minutter = sprintf("%02d", $minutter);
        $sekunder = sprintf("%02d", $sekunder);
        return "{$timer}:{$minutter}:{$sekunder}";
    }
}
