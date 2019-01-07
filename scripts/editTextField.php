<?php
require_once("../includes/config.inc.php");
require_once("../includes/functions.inc.php");
require_once("../classes/class.pdo.php");
if(!isset($db)) {
	$db = new db(DB_NAME);
}
$showDebug = false;
$devMsgs = [];

if($showDebug) {
    echo "POST:\n";
    print_recursive($_POST, 0, "\n");
    exit;
}
$rowId      = $_POST["rowId"];
$tableName  = $_POST["tableName"];
$fieldName  = $_POST["fieldName"];
$container  = $_POST["container"];
$strong  	= $_POST["strong"];
$value      = $db->get_value($tableName, $rowId, $fieldName);
$devMsgs[] = $db->sql;

inputEncode($value);

if($showDebug && !empty($devMsgs)) {
	echo implode("<br />", $devMsgs);
}

echo input_field([
    "id"        => $fieldName."_".$rowId,
    "name"      => $fieldName,
    "value"     => $value,
    "extras"    => [
        "style"             => "min-width: 350px;".($strong ? " font-weight: bold;": ""),
        "data-row-id"       => $rowId,
        "data-field-name"   => $fieldName,
        "data-table-name"   => $tableName
    ]
]);
/*
EKSEMPEL PÅ ET EDITABLE FELT:
<div id="editable-container-slug-<?php echo $table;?>-<?php echo $id;?>">
    <div
         class="editable"
         data-row-id="<?php echo $id;?>"
         data-field-name="slug"
         data-table-name="<?php echo $table;?>"
         data-container="editable-container-slug-<?php echo $table;?>-<?php echo $id;?>"
    >
        <?php echo isset($rk["slug"]) && $rk["slug"] ? $rk["slug"] : "---";?>
    </div>
</div>

SCRIPTES SÅDAN HER:
<script type="text/javascript">
    $(document).ready(function() {
       $(".editable").click(function() {
           editTextField($(this));
       });
    });
</script>
*/
?>
<script type="text/javascript">
	$(document).ready(function() {
		$("#<?php echo $fieldName."_".$rowId;?>").focus();
		$("#<?php echo $fieldName."_".$rowId;?>").blur(function(e) {
			updateTextField($(this));
		});
		$("#<?php echo $fieldName."_".$rowId;?>").on('keypress', function(e) {
			if(e.keyCode == 13) {
				e.preventDefault();
				updateTextField($(this));
			}
		});
	});
	function updateTextField(e) {
		$.post(
			"/scripts/updateTextField.php",
			{
				rowId:      e.data("row-id") || 0,
				fieldName:	e.data("field-name") || "",
				tableName:	e.data("table-name") || "",
				value:      e.val(),
				container:  '<?php echo $container;?>',
				strong:  	'<?php echo $strong;?>',
			},
			function(data) {
				if(data.length > 0) {
					$("#" + "<?php echo $container;?>").html(data);
				}
			}
		);

	}
</script>
