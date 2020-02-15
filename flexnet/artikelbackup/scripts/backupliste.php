<?php
include("./script-constants.inc.php");

if(!isset($db)) {
    $db = new db(DB_NAME);
}

$artikel_id = isset($_POST["artikel_id"]) ? intval($_POST["artikel_id"]) : 0;
if(!$artikel_id) {
    die("<p><em>artikel_id ikke angivet</em></p>");
}

$showDebug = false;
$devMsgs = [];
$table = "artikler_backup";

$backups = $db->get_rows(
    $table,
    "id DESC",
    [
        "artikel_id = {$artikel_id}"
    ],
    [
        "id",
        "rubrik",
        "created_at"
    ]
);
$devMsgs[] = $db->sql;
if($showDebug) {
    echo implode("<br />", $devMsgs);
}
    
if(empty($backups)) {
    die("<p><em>Ingen backups fundet</em></p>");
}
?>
<ul class="list-group">
    <?php foreach($backups as $backup) { ?>
        <li class="list-group-item">
            <div class="row">
                <div class="col-2 text-left">
                    <i class="fal fa-eye text-muted preview-backup mr-2" data-id="<?php echo $backup["id"];?>" title="Se backup"></i>
                    <small>
                        <?php echo $backup["id"];?>
                    </small>
                </div>
                <div class="col-8">
                    <small>
                        Gemt: <?php echo relativeDate($backup["created_at"]);?><br />
                        <?php echo $backup["rubrik"];?>
                    </small>
                </div>
                <div class="col-2 text-right">
                    <i class="fal fa-undo-alt text-muted gendan-backup" data-id="<?php echo $backup["id"];?>" title="Gendan fra denne backup"></i>
                </div>
            </div>
        </li>
    <?php } ?>
</ul>

<div class="mt-4 text-right">
    <button class="btn btn-secondary" type="button" id="genopfrisk-button">
        <i class="fal fa-sync mr-2"></i>Genopfrisk
    </button>
</div>

<script type="text/javascript">
    $(".gendan-backup").on("click", function() {
        if(confirm("Vil du overskrive artiklen med denne backup?")) {
            gendanFraBackup($(this).data("id"));
        }
    });
    $(".preview-backup").on("click", function() {
        showBackuppreview($(this).data("id"));
    });
    $("#genopfrisk-button").on("click", function() {
        backupliste();
    });
</script>