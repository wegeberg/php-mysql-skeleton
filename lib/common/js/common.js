const ADM_PATH = '/admin/';

var spinner = (txt = "") => `<i class="fal fa-spinner fa-spin fa-2x text-muted me-2"></i>${txt}`;


/* Options for små TinyMCE bokse */
var smallTinyOptions = {
    language: "da",
    height: 150,
    statusbar: false,
    menubar: false,
    relative_urls: false,
    toolbar_items_size: 'small',
    toolbar: "superscript subscript",
    setup: editor => editor.on('keyup', () => changesMade()),
    oninit:	"setPlainText",
    paste_as_text: true,
    force_p_newlines: false,
    forced_root_block: false,
    entity_encoding: 'raw',
    content_css: [ '/lib/tinymce5-css/content-small-boxes.css' ],
    branding: false,
    deprecation_warnings: false
};
async function jPost(path, data, target, callback = null) {
    $.post(
        `${ADM_PATH}scripts/${path}.php`,
        data,
        data => {
            $(`#${target}`).html(data);
            if (callback) callback();
        }
    );
}
if (typeof showGenericPreview === "undefined") {
    function showGenericPreview(url, target = "PreviewWindow") {
        const screenHeight = screen.height;
        const newWindowHeight = screenHeight - 50;
        const newWindowWidth = screen.width >= 1200 ? 1200 : screen.width;
        window.open(url, target, "width=" + newWindowWidth + ",height=" + newWindowHeight + ",status=yes,scrollbars=yes,toolbar=yes,location=yes");
    }
}
if (typeof delay === "undefined") {
    function delay(fn, ms) {
        let timer = 0
        return function (...args) {
            clearTimeout(timer)
            timer = setTimeout(fn.bind(this, ...args), ms || 0)
        }
    }
}
if (typeof updateField === "undefined") {
    var updateField = el => {
        const data = el.data();
        data.value = el.html().trim();
        $.post(
            "/lib/common/scripts/update.php",
            { ...data },
            response => response.length > 0 && console.log(response)
        );
    };
}
if (typeof toggleField === "undefined") {
    var toggleField = el => {
        const data = el.data();
        data.checked = el.is(":checked") ? 1 : 0;
        $.post(
            "/lib/common/scripts/toggle.php",
            { ...data },
            response => response.length > 0 && console.log(response)
        );
    };
}
if (typeof editDateTimeField === "undefined") {
    var editDateTimeField = (field, id, table, strong) => {
        const target = `#${field}_${id}`;
        $.post(
            "/lib/common/scripts/editDateTimeField.php",
            { field, id, table, strong },
            data => {
                if(data.length > 0) {
                    $(target).html(data);
                }
            }
        );
    }
}
if (typeof updateDateTimeField === "undefined") {
    var updateDateTimeField = params => {
        $.post(
            "/lib/common/scripts/updateDateTimeField.php",
            { ...params },
            data => {
                if(data && data?.length > 0) {
                    $(`#${params.field}_${params.id}`).html(data);
                } else {
                    console.log("UPS...");
                }
            }
        );
    };
}

if (typeof SetCookie === "undefined") {
    function SetCookie(cookieName, cookieValue, nDays) {
        const today = new Date();
        const expire = new Date();
        if (nDays === null || nDays === 0) { 
            nDays = 730; 
        }
        expire.setTime(today.getTime() + 3600000 * 24 * nDays);
        document.cookie = cookieName + "=" + escape(cookieValue) + ";expires=" + expire.toGMTString();
    }
}
if (typeof ReadCookie === "undefined") {
    function ReadCookie(cookieName) {
        const theCookie = " " + document.cookie;
        const ind = theCookie.indexOf(" " + cookieName + "=");
        if (ind === -1) { ind = theCookie.indexOf(";" + cookieName + "="); }
        if (ind === -1 || cookieName === "") { return ""; }
        const ind1 = theCookie.indexOf(";", ind + 1);
        if (ind1 === -1) { 
            ind1 = theCookie.length; 
        }
        return unescape(theCookie.substring(ind + cookieName.length + 2, ind1));
    }
}
if (typeof DeleteCookie === "undefined") {
    function DeleteCookie(cookieName) {
        document.cookie = cookieName + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    }
}