<?php
include("../includes/config.inc.php");
include("../includes/functions.inc.php");
include("../classes/class.pdo.php");
if(!isset($db)) {
	$db = new db(DB_NAME);
}
$showDebug = false;
$devMsgs = [];

if(!isset($_POST["rowId"]) || !$_POST["rowId"]) {
	print_recursive($_POST);
	die("rowId mangler");
}

$rowId      = $_POST["rowId"];
$tableName  = $_POST["tableName"];
$fieldName  = $_POST["fieldName"];
$value      = $_POST["value"];
$container  = $_POST["container"];
$strong  	= $_POST["strong"];

$bynavn = "";
if($fieldName == "postnr") {
	$bynavn = $db->get_value("postnumre", 0, "bynavn", "id ASC", "postnummer = {$value}");
}
if($bynavn) {
	$db->update($tableName, $rowId, [
		$fieldName  => $value,
		"bynavn"	=> $bynavn,
		"edited_at"	=> date(DATOFORMAT),
		"edited_by" => isset($_SESSION["login_id"]) ? $_SESSION["login_id"] : 0
	]);
} else {
	$db->update($tableName, $rowId, [
		$fieldName  => $value,
		"edited_at"  => date(DATOFORMAT),
		"edited_by" => isset($_SESSION["login_id"]) ? $_SESSION["login_id"] : 0
	]);
}

//if($fieldName == "slug") { // slugs has to be unique
//
//}

$devMsgs[] = $db->sql;

if($showDebug && !empty($devMsgs)) {
	echo implode("<br />", $devMsgs);
}
?>
<div
	class="editable"
	data-row-id="<?php echo $rowId;?>"
	data-field-name="<?php echo $fieldName;?>"
	data-table-name="<?php echo $tableName;?>"
	data-container="<?php echo $container;?>"
	data-strong="<?php echo $strong;?>"
	title="Klik for at redigere"
	<?php echo $fieldName == "postnr" ? "title=\"{$bynavn}\"" : "";?>
	<?php echo intval($strong) == 1 ? ' style="font-weight: bold;"' : '';?>
>
	<?php echo $fieldName == "tlf" ? str_replace(" ", "", $value) : (strlen(trim($value)) > 0 ? $value : "---");?>
</div>
<script type="text/javascript">
	$(function() {
		$(".editable").click(function() {
			editTextField($(this));
		});
	});
</script>
