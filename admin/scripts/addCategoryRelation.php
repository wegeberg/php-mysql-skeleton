<?php
include("../../includes/constants.inc.php");
include("../../classes/class.pdo.php");
if (!isset($db)) {
	$db = new db(DB_NAME);
}

$article_id = isset($_POST["article_id"]) ? intval($_POST["article_id"]) : 0;
$category_id = isset($_POST["category_id"]) ? intval($_POST["category_id"]) : 0;

if (!$article_id || !$category_id) {
    die("<p><small>article_id og category_id missing</small></p>");
}

// delete relation if it exists
$db->delete(
    "article_category_rel", 
    0, 
    [ "article_id = {$article_id}", "category_id = {$category_id}" ]
);

// insert relation
$db->insert(
    "article_category_rel",
    [ "article_id" => $article_id, "category_id" => $category_id ],
    "id",
    true
);
