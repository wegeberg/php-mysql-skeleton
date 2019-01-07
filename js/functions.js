// JavaScript Document
// JavaScript Document
function underkategorierSelect(hovedkategoriId, name, selectedId, target, callback) {
	'use strict';
	target = target || 'underkategorier-select';
	name = name || 'underkategori_id';
		callback = callback || null;
	selectedId = selectedId || 0;
	$.post(
		'/scripts/underkategorierSelect.php',
		{
			hovedkategoriId: hovedkategoriId,
			name: name,
			selectedId: selectedId,
		},
		function(data) {
			$(`#${target}`).html(data);
			if(callback) {
					callback();
			}
		}
	)
}
function editTextField(element) {
	"use strict";
	const container = element.data("container") || "";
	$.post(
			"/scripts/editTextField.php",
			{
					rowId:      element.data("row-id") || 0,
					fieldName:	element.data("field-name") || "",
					tableName:	element.data("table-name") || "",
					strong:     element.data("strong") || "0",
					container:  container
			},
			function(data) {
					if(data.length > 0) {
							$("#" + container).html(data);
					}
			}
	);
}
function hjemmeside(url) {
    "use strict";
    if(!url) {
        return "";
    }
    var retur = url.trim();
    if(!retur || retur.substring(0, 7) === "http://" || retur.substring(0, 8) === "https://") {
        return retur;
    }
    return "http://" + retur;
}
function underkategoriSelect(hovedkategoriId) {
    "use strict";
    $.post(
        "/scripts/underkategoriSelect.php",
        {
            hovedkategoriId: hovedkategoriId,
        },
        function(data) {
            $("#underkategori-select-div").html(data);
        }
    );
}
function deleteRecord(table, id, target, bevar) {
	"use strict";
	target = target || null;
	bevar = bevar || null;
	$.post(
		"/scripts/deleteRecord.php",
		{
			table: table,
			id: id,
			bevar: bevar
		},
		function(data) {
			if(data.length > 0 && target) {
				$("#target").html(data);
			}
		}
	)
}
function hentBynavn(postnr, target) {
	"use strict";
	target = target || "bynavn";
	$.post(
		"/scripts/hentBynavn.php",
		{
			postnr: postnr
		},
		function(data) {
			$("#" + target).val(data);;
		}
	);
}
function getCookie(name) {
	"use strict";
    const v = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
    return v ? v[2] : null;
}
function setCookie(name, value, days) {
    "use strict";
    const d = new Date;
    d.setTime(d.getTime() + 24*60*60*1000*days);
    document.cookie = name + "=" + value + ";path=/;expires=" + d.toGMTString();
}
function deleteCookie(name) {
    "use strict";
    setCookie(name, '', -1);
}
