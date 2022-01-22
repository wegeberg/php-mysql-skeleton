<?php
include("../../includes/constants.inc.php");
include("../../classes/class.pdo.php");
if (!isset($db)) {
	$db = new db(DB_NAME);
}
$article_id = isset($_POST["article_id"]) ? intval($_POST["article_id"]) : 0;
$category_id = isset($_POST["category_id"]) ? intval($_POST["category_id"]) : 0;

if (!$article_id || !$category_id) {
    die("article_id og category_id missing");
}

$db->delete(
    "article_category_rel", 
    0, 
    [ "article_id = {$article_id}", "category_id = {$category_id}"]
);
