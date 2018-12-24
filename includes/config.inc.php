<?php
	/* CONFIG-FIL */

	if(!defined('CONFIG')) {
		define('CONFIG','Config included');
		if(session_id() == "" || !isset($_SESSION)) {
			session_start();
		}
		$admlogin = (isset($_SESSION['login_id']) && trim($_SESSION['login_id']) != "");

		if(!defined("ABSPATH")) define("ABSPATH", str_replace("/includes", "", dirname(__FILE__))."/");
		if(!defined("DATOFORMAT")) define("DATOFORMAT", "Y-m-d H:i:s");
		if(!defined("KORTDATOFORMAT")) define("KORTDATOFORMAT", "Y-m-d");
		if(!defined("COOKIEUDLOEB")) define("COOKIEUDLOEB", time() + (2*365*24*60*60));
		date_default_timezone_set("Europe/Copenhagen");

		$website = "";

		if(!isset($logindSide) && !$admlogin) {
			header("Location:/index.php");
		}

		// DATABASE
		define("DB_NAME", "");
		define("DB_USER", "");
		define("DB_PASS", "");
		define("DB_HOST", "localhost");
		define("DB_DEBUG", false);

		define("DEBUG_FILE", "sql_error.log");
		define("DEBUG_LOGFILE", "../sql_debug.log");
		define("DISPLAY_DEBUG", false);
		define("CHARSET", "UTF-8");
		define("DBCHARSET", "utf8");

		$imageTypes = ["gif", "jpg", "png", "jpeg", "svg"];
		$documentTypes = array_merge($imageTypes, [
				"pdf",
				"doc",
				"docx",
				"xls",
				"xlsx",
				"ppt",
				"pptx"
			]
		);
	}
?>
