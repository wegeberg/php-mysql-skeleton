<?php
include("../includes/config.inc.php");
include("../lib/common/common-functions.inc.php");
include("../classes/class.pdo.php");
if (!isset($db)) {
	$db = new db(DB_NAME);
}
$menuPoint = "articles";
$activeUser = $db->get_row("admin_users", ADMIN_USER_ID);

$id = isset($_REQUEST["id"]) ? intval($_REQUEST["id"]) : 0;

if ($_POST) {
	$_POST["edited_by"] = ADMIN_USER_ID;
	if ($id) {
		// Update existing article
		$db->update(
			"articles",
			$id,
			$_POST
		);
	} else {
		$_POST["created_by"] = ADMIN_USER_ID;
		$db->insert("articles", $_POST);
		$id = $db->lastid();
	}
}

$article = $id && !isset($article) ? $db->get_row("articles", $id) : null;
?>
<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Flexnet" />
    <meta name="author" content="Martin Wegeberg" />
    <title>Article</title>

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

	<!-- JQUERY UI -->
	<link href="https://code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css" rel="stylesheet" type="text/css">

    <!-- JQUERY -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- JQUERY UI -->
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js" crossorigin="anonymous"></script>
    <link href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" rel="stylesheet" type="text/css">
	
	<!-- FONT AWESOME ICONS  -->
	<link rel="stylesheet" href="/lib/fontawesome-free-6.7.2-web/css/all.css" />
	
	<!-- TOASTR -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

	<link rel="stylesheet" type="text/css" href="/css/style.css?v=<?php echo defined("VERSION") ? VERSION : "1.0";?>">
	
</head>
<body>
    <main class="flex-shrink-0">
        <?php include("../includes/menu.inc.php"); ?>

        <div class="container-fluid">
            <?php if (isset($showDebug) && $showDebug && !empty($devMsgs)) { ?>
				<div class="alert alert-warning"><?php print_recursive($devMsgs);?></div>
			<?php } ?>

			<div class="row mt-4">
				<div class="col-12 col-md-6 offset-md-3">
					<form 
						role="form" 
						target="_self"
						method="post"
						accept-charset="UTF-8"
						class="mb-5"
						id="article-form"
					>
						<input type="hidden" id="id" name="id" value="<?php echo $id;?>" />
						<div class="card">
							<div class="card-header">
								<div class="d-flex justify-content-between">
									<span>Article</span>
									<button
										type="button"
										class="btn btn-success btn-sm save-button"
										style="display: none;"
									>
										Save article
									</button>
								</div>
							</div>
							<div class="card-body">
								<div class="mb-3">
									<?php
									echo input_field([
										"name"			=> "title",
										"value"			=> $article ? $article["title"] : "",
										"placeholder"	=> "Enter the title"
									]);
									?>
								</div>
								<div class="mb-3">
									<?php
									echo textarea([
										"name"		=> "subtitle",
										"value"		=> $article ? $article["subtitle"] : "",
										"extras"	=> [ "rows" => 5 ]
									]);
									?>
								</div>
								<div class="mb-3">
									<?php if (!$id) { ?>
										<p>
											<em>
												Save article to add category relations
											</em>
										</p>
									<?php } else { ?>
										<?php
										$categories = $db->get_rows("categories", "name ASC");
										if ($categories) {
											echo select([
												"name"			=> "category_id",
												"options"		=> $categories,
												"firstText"		=> "Select category to add relation",
												"valueFields"	=> [ "name", "description"]
											]);
										}
										?>
										<div id="related-categories" class="pt-3 clearfix"></div>
									<?php } ?>
								</div>

								<div class="mb-3">
									<?php
									echo textarea([
										"name"		=> "bodytext",
										"value"		=> $article ? $article["bodytext"] : "",
										"noLabel"	=> true,
										"styles"	=> ["height: 600px" ]
									]);
									?>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>

        </div>
        <!-- /.container-fluid -->
    </main>
    
    <footer class="footer bg-light">
        <div class="container">
            <span class="text-muted">Martin Wegeberg &bull; Flexnet 2019
                <?php echo intval(date("Y")) !== 2019 ? "-" . date("Y") : ""; ?>
            </span>
        </div>
    </footer>

	<!-- BOOTSTRAP -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
	
	<!-- TOASTR -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

	<!-- COMMON FUNCTIONS -->
	<script src="/lib/common/js/common.js?v=<?php echo defined("VERSION") ? VERSION : "1.0";?>"></script>
	
    <script>
		<?php if (isset($msgSuccess) && $msgSuccess) { ?>
			toastr.success('<?php echo $msgSuccess;?>');
		<?php } ?>
		<?php if (isset($msgError) && $msgError) { ?>
			toastr.error('<?php echo $msgError;?>');
		<?php } ?>
	</script>


	<!-- TINYMCE 5 -->
    <script src="/lib/tinymce5/tinymce.min.js"></script>

	<script>
        const saveButtons = $(".save-button");
		const article_id = $("#id").val();

		$(function () {
			+article_id > 0 && articleCategories();
		});

		const articleCategories = () => {
			console.log("articleCategories");
			$.post(
				"./scripts/articleCategories.php", 
				{ article_id },
				data => $("#related-categories").html(data)
			);
		}
		const addCategoryRelation = () => {
			console.log("addCategoryRelation");
			const category_id = $("#category_id option:selected").val();
			$.post(
				"./scripts/addCategoryRelation.php",
				{ article_id, category_id },
                () => articleCategories()
			);
		}

		$("#category_id").on('change', () => addCategoryRelation());



		$(function () { $('[data-bs-toggle="tooltip"]').tooltip() });

		var formChanged = false;
		function changesMade() {
			formChanged = true;
			saveButtons.fadeIn();
		}

		// Trigger changesMade
		$("input[type=text], input[type=number], textarea").on('keyup change', function () {
			!$(this).hasClass("non-trigger") && changesMade();
		});
		$("input[type=checkbox]").on('click', function () {
			!$(this).hasClass("non-trigger") && changesMade();
		});
		
		// $("select").on('change', function() {
		// 	!$(this).hasClass("non-trigger") && changesMade();
		// });

		// Warning on changed
		window.addEventListener('beforeunload', function (e) {
			if(formChanged) {
				const msg = "Changes not saved!";
				e.returnValue = msg;
				return msg;
			}
		});

		// Submit form
		saveButtons.on('click', () => {
			formChanged = false;
			$("#article-form").submit();
		});

		tinymce.init({
			...smallTinyOptions,
			selector: "#bodytext",
			plugins: ['wordcount'],
			statusbar: true,
			setup: editor => {
				editor.on('change', () => changesMade());
				editor.on('init', () => {
					// Change from word count to character count
					$(editor.getContainer()).find('button.tox-statusbar__wordcount').click();
				});
			},
		});
	</script>

</body>
</html>
