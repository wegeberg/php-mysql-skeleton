<?php
include("../includes/config.inc.php");
include("../includes/functions.inc.php");
include("../classes/class.pdo.php");
if(!isset($db)) {
	$db = new db(DB_NAME);
}
$showDebug = false;
$devMsgs = [];

$filId = isset($_POST["filId"]) ? intval($_POST["filId"]) : 0;
$relField = isset($_POST["relField"]) ? $_POST["relField"] : null;
$masterTable = isset($_POST["masterTable"]) ? $_POST["masterTable"] : null;
$table = isset($_POST["table"]) ? $_POST["table"] : null;

if(!$relField || !$masterTable || !$table || !$filId) {
	print_recursive($_POST);
	die("parameter mangler");
}

$fil = $db->get_row($table, $filId);
$devMsgs[] = $db->sql;
if(!empty($fil)) {
	if($fil["filnavn"]) {
		$filsti = ABSPATH."medier/{$masterTable}/".$fil[$relField]."/".$fil["filnavn"];
		unlink($filsti);
		$devMsgs[] = "filsti: {$filsti}";
		if(isset($fil["resized"]) && $fil["resized"]) {
				unlink(ABSPATH."medier/{$masterTable}/".$fil[$relField]."/".$fil["resized"]);
		}
	}
	$db->delete($table, $filId);
	$devMsgs[] = $db->sql;
}

if($showDebug and !empty($devMsgs)) { ?>
	<div class="alert alert-warning">
		<?php echo implode("<br />", $devMsgs);?>
	</div>
<?php } ?>
