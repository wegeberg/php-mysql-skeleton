const grundsti = "/flexnet/artikelbackup/"
const script_path = `${grundsti}scripts/`;
const artikel_tabel = "artikler";
const backup_tabel = "artikler_backup";
const spinner_id = "#backup-spinner";
const liste_id = "#backupliste";
if(typeof form_id == "undefined") {
    const form_id = "#artikel-form";
}
const artikel_path = "/artikel.php";
const preview_path = `${grundsti}scripts/backup-preview.php`;

function lavBackup(slet_ikke_id) {
    slet_ikke_id = slet_ikke_id || 0;
    if(spinner_id) {
        $(spinner_id).show();
    }
    $.post(
        `${script_path}lavBackup.php`,
        $(form_id).serialize() + `&slet_ikke_id=${slet_ikke_id}`,
        function(data) {
            if(data.success) {
                console.log(data.result);
            } else {
                console.log(data.error);
            }
            if(data.debug) {
                console.log(data.debug);
            }
            backupliste(); // Genindlæs backupliste - der kan være ændret i den
            setTimeout(() => {
                $(spinner_id).hide();
            }, 5000);
        }
    );
    
}
function backupliste() {
    $.post(
        `${script_path}backupliste.php`,
        {
            artikel_id: $("#id").val()
        },
        function(data) {
            $(liste_id).html(data);
        }
    );
}
function gendanFraBackup(backup_id) {
    lavBackup(backup_id);
    $.post(
        `${script_path}gendanFraBackup.php`,
        $(form_id).serialize() + `&backup_id=${backup_id}`,
        function(data) {
            console.log(data);
            formChanged = false; // For at undgå gemme-alert
            window.location.replace(artikel_path + "?id=" + $("#id").val());
        }
    )
};
function showBackupPreview(backup_id) {
	const screenHeight = screen.height;
	const newWindowHeight = screenHeight - 50;
	const newWindowWidth = 1200;
	window.open(
        `${preview_path}?id=${id}`,
        "BU-preview", 
        `width=${newWindowWidth},height=${newWindowHeight},status=yes,scrollbars=yes,toolbar=yes,location=yes`
    );
}
$(document).ready(function() {
    let changedSinceLastBackup = false;
    if(+$("#id").val() > 0) {
        interval_id = setInterval(() => {
            if(changedSinceLastBackup) {
                lavBackup();
                changedSinceLastBackup = false;
            }
        }, 5 * 60 * 1000);
    }
});