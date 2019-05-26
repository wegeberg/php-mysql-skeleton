<?php
if(!defined("CONSTANTS_INCLUDED")) {
    define("CONSTANTS_INCLUDED", true);

    define("VERSION", "1.0");

    if(!defined("ABSPATH")) {
        define("ABSPATH", dirname( __FILE__, 2)."/");   
    }

    date_default_timezone_set("Europe/Copenhagen");
	if(!defined("DATOFORMAT")) define("DATOFORMAT", "Y-m-d H:i:s");
	if(!defined("KORTDATOFORMAT")) define("KORTDATOFORMAT", "Y-m-d");
    if(!defined("COOKIEUDLOEB")) define("COOKIEUDLOEB", time() + (2*365*24*60*60));
    
	// DATABASE
	define("DB_NAME", "");
	define("DB_USER", "");
	define("DB_PASS", "");
	define("DB_HOST", "localhost");
	define("DB_DEBUG", false);
	define("CHARSET", "UTF-8");
	define("DBCHARSET", "utf8");

    define("IMAGE_TYPES", ["gif", "jpg", "png", "jpeg", "svg", "webp"]);
    define("DOCUMENT_TYPES", array_merge(IMAGE_TYPES, [
        "pdf",
        "doc",
        "docx",
        "xls",
        "xlsx",
        "ppt",
        "pptx"
    ]));
}
?>