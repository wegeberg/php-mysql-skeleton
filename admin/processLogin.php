<?php
include("../includes/constants.inc.php");
include("../classes/class.pdo.php");

if(!isset($db)) {
	$db = new db(DB_NAME);
}

$indexUrl = "/".ADM_PATH;

// If request for sub page - redirect to this after login
$targetUrl = isset($_POST["url"]) && $_POST["url"] ? $_POST["url"] : "";

$redirectUrl = $targetUrl 
    ?   urldecode($targetUrl)
    :   "/".ADM_PATH."articles.php";

if (isset($_GET["logout"])) {
	setcookie(
        ID_COOKIE_NAME,
        "",
        time() - 3600,
        "/"
    );
	header("Location: {$indexUrl}");
	exit;
}

$table      = "admin_users";
$showDebug  = false;
$devMsgs    = [];
$email		= trim($_POST["email"]);
$password	= trim($_POST["password"]);

if (!$email) {
	header("Location: {$indexUrl}?err=".urlencode("Email missing")."&url={$targetUrl}");
    exit;
}
if (!$password) {
	header("Location: {$indexUrl}?err=".urlencode("Password missing")."&url={$targetUrl}");
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	header("Location: {$indexUrl}?err=".urlencode("Invalid email!")."&url={$targetUrl}");
    exit;
}
$user = $db->get_row(
	$table, 
	0, 
	[ "deleted = 0", "email LIKE '{$email}'" ]
);
$devMsgs[] = $db->sql;

if (empty($user)) {
	header("Location: {$indexUrl}?err=".urlencode("User not found")."&url={$targetUrl}");
    exit;
}

if (
    password_verify ($password, $user["password_hash"]) || 
    (strlen ($user["password"]) > 0 && $password == $user["password"])
) {
	setcookie(
        ID_COOKIE_NAME,
        $user["id"],
        COOKIE_EXPIRY,
        "/"
    );
	setcookie(
        EMAIL_COOKIE_NAME,
        $user["email"],
        COOKIE_EXPIRY,
        "/"
    );


    /* USER ROLES */
    $userRoleIds = $db->get_distinct(
		"admin_rights", 
		"role_id", 
		"role_id ASC",
		"user_id = {$user['id']}"
	);
    if (!$userRoleIds) {
        header("Location: {$indexUrl}?err=".urlencode("Missing user rights")."&url={$targetUrl}");
    }
    if (!array_intersect(ACCESS_ROLES, $userRoleIds)) {
        header("Location: {$indexUrl}?err=".urlencode("Missing user rights (2)")."&url={$targetUrl}");
        exit;
    }

	header("Location: {$redirectUrl}"); 
    exit;
}
header("Location: {$indexUrl}?err=".urlencode("Wrong password")."&url={$targetUrl}");
