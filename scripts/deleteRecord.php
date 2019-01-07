<?php
include("../includes/config.inc.php");
include("../classes/class.pdo.php");
if(!isset($db)) {
	$db = new db(DB_NAME);
}
$showDebug = false;
$devMsgs = [];

if(!isset($_POST["table"]) || !isset($_POST["id"])) {
	echo "Manglende table eller id";
	exit;
}
$devMsgs[] = "table: {$_POST["table"]} - id: {$_POST["id"]}";

if(isset($_POST["bevar"]) && intval($_POST["bevar"]) == 1) {
	$db->update($_POST["table"], $_POST["id"], [
		"slettet"	=> 1
	]);
} else {
	$db->delete($_POST["table"], $_POST["id"]);
}
$devMsgs[] = $db->sql;

if($showDebug && !empty($devMsgs)) {
	echo implode("<br />", $devMsgs);
}
?>
