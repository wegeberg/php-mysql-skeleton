<?php
/* Updates the value of a text field */
include("../../../includes/constants.inc.php");
include("../../../classes/class.pdo.php");
if (!isset($db)) {
    $db = new db(DB_NAME);
}
$showDebug = false;
$devMsgs = [];

$id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;
$table = isset($_POST["table"]) ? $_POST["table"] : null;
$field = isset($_POST["field"]) ? $_POST["field"] : null;
$value = isset($_POST["value"]) ? $_POST["value"] : null;

if (!$id) die("id mangler");
if (!$table) die("table mangler");
if (!$field) die("field mangler");

$db->update(
    $table,
    $id,
    [ $field => $value ? $value : null ],
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
