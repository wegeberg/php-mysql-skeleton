<?php
/* Toggles the value of a binary field */
include("../../../includes/constants.inc.php");
include("../../../classes/class.pdo.php");
if (!isset($db)) {
    $db = new db(DB_NAME);
}
$showDebug = false;
$devMsgs = [];

$id         = isset($_POST["id"])    ? intval($_POST["id"]) : 0;
$table      = isset($_POST["table"]) ? $_POST["table"] : null;
$field      = isset($_POST["field"]) ? $_POST["field"] : null;
$checked    = isset($_POST["checked"]) && $_POST["checked"];

if (!$id)    die ("id missing");
if (!$table) die ("table missing");
if (!$field) die ("field missing");

$db->update(
    $table,
    $id,
    [ $field => $checked ? "1" : "0" ],
    null,
    "id",
    true
);
$devMsgs[] = $db->realQuery;
if($showDebug) {
    array_map(function($msg) {
        echo $msg.PHP_EOL;
    }, $devMsgs);
}
