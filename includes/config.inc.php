<?php
/* CONFIG-FILE */

if(!defined("CONFIG")) {
	define("CONFIG", true);

	include(dirname(__FILE__)."/constants.inc.php");

	if(session_id() == "" || !isset($_SESSION)) {
		session_start();
	}
	
	$notLoggedInUrl = ADM_PATH."?err="
		.urlencode ("Not logged in")
		."&url="
		.urlencode ($_SERVER["REQUEST_URI"]);

	if (!ADMIN_USER_ID) {
		header("Location: {$notLoggedInUrl}");
	}

	include(ABSPATH."classes/class.pdo.php");
    if(!isset($db)) {
        $db = new db(DB_NAME);
    }

	define(
        "ADMIN_USER_ROLES",
        $db->get_distinct(
            "admin_rights", 
            "role_id", 
            "role_id ASC",
            "user_id = ".ADMIN_USER_ID
        )
    );

	// Check user rights?
	if (!array_intersect(ADMIN_USER_ROLES, ACCESS_ROLES)) {
		header("Location: {$notLoggedInUrl}");
	}
}
?>
