<?php
include("../../../includes/constants.inc.php");
include("../common-functions.inc.php");
include("../../../classes/class.pdo.php");
if (!isset($db)) {
	$db = new db(DB_NAME);
}

$showDebug = false;
$devMsgs = [];

$table = $_POST["table"] ?? null;
$field = $_POST["field"] ?? null;
$id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;
$strong = isset($_POST["strong"]) && $_POST["strong"];

$rk = $db->get_row($table, $id);

if (!$rk) die ("rk ikke fundet");

$formateretDato = $rk[$field]
	?	date("d/m/Y", strtotime($rk[$field]))
	:	date("d/m/Y");
$hours = date("H", strtotime($rk[$field]));
$minutes = date("i", strtotime($rk[$field]));

$dateFieldId = "dato_{$field}_{$id}";

if($showDebug) print_recursive($_POST);
?>
<input 
	type="text" 
	id="<?php echo $dateFieldId;?>" 
	value="" 
	class="form-control dateEditField datepicker" 
	placeholder="Dato" 
	data-id="<?php echo $id;?>" 
	data-table="<?php echo $table;?>" 
	data-field="<?php echo $field;?>" 
	data-strong="<?php echo $strong;?>" 
	style="width: 150px;" 
/>
<label>kl:</label>
<select id="<?php echo $dateFieldId;?>_hours" class="form-control w-auto d-inline">
	<?php for($h = 0; $h < 24; $h++) { ?>
		<option
			value="<?php echo sprintf('%02d', $h);?>"
			<?php echo ($h == $hours ? 'selected="selected"' : '');?>
		>
			<?php echo sprintf('%02d', $h);?>
		</option>
	<?php } ?>
</select>

<select id="<?php echo $dateFieldId;?>_minutes" class="form-control w-auto d-inline">
	<option value="00" <?php echo ($minutes == "00" ? 'selected="selected"' : '');?>>00</option>
	<option value="15" <?php echo ($minutes == "15" ? 'selected="selected"' : '');?>>15</option>
	<option value="30" <?php echo ($minutes == "30" ? 'selected="selected"' : '');?>>30</option>
	<option value="45" <?php echo ($minutes == "45" ? 'selected="selected"' : '');?>>45</option>
</select>
<div class="mb-3 mt-1">
	<button class="btn btn-outline-success btn-sm w-auto d-inline" id="save-button-<?php echo $field;?>">
		Save time
	</button>
</div>

<script>
	$(function() {
		$.datepicker.setDefaults($.datepicker.regional["da"]);
		$(".datepicker").datepicker({
			dateFormat:'dd/mm/yy',
			clickInput:true,
			startDate:'<?php echo date("d/m/Y");?>'
		});
		$("#<?php echo $dateFieldId;?>").val('<?php echo $formateretDato;?>');
	});
	
	$("#save-button-<?php echo $field;?>").on('click', function() {
		const field = '#<?php echo $dateFieldId;?>';
		const data = $(field).data();
		data.dato = $(field).val();
		data.hours = $(`${field}_hours option:selected`).val();
		data.minutes = $(`${field}_minutes option:selected`).val();
		delete data.datepicker;
		updateDateTimeField({ ...data });
	});
	</script>
