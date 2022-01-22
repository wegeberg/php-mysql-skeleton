<?php
/* Updates the value of a timestamp field */
include("../../../includes/constants.inc.php");
include("../common-functions.inc.php");
include("../../../classes/class.pdo.php");
if(!isset($db)) {
	$db = new db(DB_NAME);
}

$showDebug = false;
$devMsgs = array();

$table = isset($_POST["table"]) ? $_POST["table"] : null;
$field = isset($_POST["field"]) ? $_POST["field"] : null;
$id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;
$date = isset($_POST["date"]) ? $_POST["date"] : null;
$hours = isset($_POST["hours"]) ? intval($_POST["hours"]) : 0;
$minutes = isset($_POST["minutes"]) ? intval($_POST["minutes"]) : 0;

$val = swap_date($date, 'dkus')." ".sprintf('%02d',$hours).":".sprintf('%02d',$minutes).":00";

$db->update(
	$table,
	$id,
	[ $field => $val ]
);
$devMsgs[] = $db->sql;
echo editableDateTimeField($table, $field, $id, $val, false);
if($showDebug) {
	echo implode("<br />", $devMsgs);
}