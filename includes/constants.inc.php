<?php
if(!defined ("CONSTANTS_INCLUDED")) {
    define ("CONSTANTS_INCLUDED", true);

    if(!defined ("VERSION")) {
        define ("VERSION", "1.0.0");
        define ("VERSION_DATO", "2022-01-21");
    }

    if (!defined ("ID_COOKIE_NAME")) {
        define ("ID_COOKIE_NAME", "flexnet_admin_id");
        define ("EMAIL_COOKIE_NAME", "flexnet_admin_email");
    }

    if(!isset($_SESSION) || !session_id()) {
        session_start();
    }

    define(
        "ADMIN_USER_ID",
        isset ($_COOKIE[ ID_COOKIE_NAME ]) && 
        $_COOKIE[ ID_COOKIE_NAME ]
            ?   intval ($_COOKIE[ ID_COOKIE_NAME ])
            :   0
    );
    // Which roles has access to this module?
    if (!defined("ACCESS_ROLES")) {
		define("ACCESS_ROLES", [1, 2]); // administrator and journalist
	}

    if (!defined ("ABSPATH")) {
        define ("ABSPATH", dirname( __FILE__, 2)."/");   
    }

    if (!defined("ADM_PATH")) {
        define("ADM_PATH", "admin/");
    }

    date_default_timezone_set("Europe/Copenhagen");
	if (!defined ("DATEFORMAT")) {
        define ("DATEFORMAT", "Y-m-d H:i:s");
    }
	if(!defined ("SHORT_DATEFORMAT")) {
        define ("SHORT_DATEFORMAT", "Y-m-d");
    }
    if (!defined ("COOKIE_EXPIRY")) {
        define ("COOKIE_EXPIRY", time() + (2 * 365 * 24 * 60 * 60));
    }

	// DATABASE
    if (!defined ("DB_NAME")) {
        define ("DB_NAME", "flexnet");
        define ("DB_USER", "flexnet");
        define ("DB_PASS", "flexnet");
        define ("DB_HOST", "localhost");
        define ("DB_DEBUG", false);
        define ("DBCHARSET", "utf8mb4");
    }

    if (!defined ("IMAGE_TYPES")) {
        define (
            "IMAGE_TYPES",
            [ "gif", "jpg", "png", "jpeg", "svg", "webp" ]
        );
        define (
            "DOCUMENT_TYPES", 
            array_merge (
                IMAGE_TYPES, 
                [
                    "pdf",
                    "doc",
                    "docx",
                    "xls",
                    "xlsx",
                    "ppt",
                    "pptx"
                ]
            )
        );
    }
}
?>