<?php
/* CONFIG-FIL */

if(!defined("CONFIG")) {
	define("CONFIG", true);

	if(!defined("ABSPATH")) {
		define("ABSPATH", dirname( __FILE__, 2)."/");   
	}
	require_once(ABSPATH."includes/constants.inc.php");

	if(session_id() == "" || !isset($_SESSION)) {
		session_start();
	}
	define("ADM_LOGIN", isset($_SESSION['login_id']) && trim($_SESSION['login_id']) != "");

	if(!isset($logindSide) && !ADM_LOGIN) {
		header("Location:/index.php");
	}
}
?>
