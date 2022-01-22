<?php
include("../../includes/constants.inc.php");
include("../../classes/class.pdo.php");
if (!isset($db)) {
	$db = new db(DB_NAME);
}

// Example of debugging
$showDebug = false;
$devMsgs = [];

function category_tag($category, $article_id) {
    return <<<CATEGORY
    <div 
        class="float-start remove-category me-2" 
        data-category_id="{$category["id"]}"
        data-article_id="{$article_id}"
        style="background-color: slategrey; color: white; padding: 5px 8px;border-radius: 5px; cursor: pointer;"
        title="Remove relation"
    >
        {$category["name"]}
        <i class="fal fa-times ms-1"></i>
    </div>
CATEGORY;
}

$article_id = isset($_POST["article_id"]) ? intval($_POST["article_id"]) : 0;

if (!$article_id) {
    die("article_id missing");
}

$relatedCategories = $db->get_rows(
    "categories",
    "name ASC",
    [ "id IN ( SELECT category_id FROM article_category_rel WHERE article_id = {$article_id} )" ]
);
$devMsgs[] = $db->sql;

if (!$relatedCategories) {
    if($showDebug) {
        echo implode("<br />", $devMsgs);
    }    die("<p><em><small>No related categories</small></em></p>");
}

array_map(function($category) use ($article_id) {
    echo category_tag($category, $article_id);
}, $relatedCategories);


if($showDebug) {
    echo '<div>'.implode("<br />", $devMsgs).'</div>';
}
?>

<script>
    $(".remove-category").on('click', function() {
        $.post(
            "./scripts/removeCategoryRelation.php",
            $(this).data(),
            response => articleCategories()
        );
    });
</script>