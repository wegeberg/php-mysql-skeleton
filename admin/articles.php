<?php
include("../includes/config.inc.php");
include("../lib/common/common-functions.inc.php");
include("../classes/class.pdo.php");
if (!isset($db)) {
    $db = new db(DB_NAME);
}

$menuPoint = "articles";

$articles = $db->get_rows("articles", "id ASC");
?>
<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Flexnet" />
    <meta name="author" content="Martin Wegeberg" />
    <title>Articles</title>

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

			<div>
				<h1 class="d-inline-block">Articles</h1>
				<a role="button" href="article.php" class="btn btn-primary float-end mt-2">
					New article
				</a>
			</div>

			<?php if (!$articles) { ?>
				<p>
					<small>
						<em>No articles found</em>
					</small>
				</p>
			<?php } else { ?>
				<table class="table">
					<thead>
						<tr>
							<th class="text-end">
								#
							</th>
							<th>
								Title
							</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($articles as $article) { ?>
							<tr>
								<td class="text-end" style="width: 50px;">
									<?php echo $article["id"];?>
								</td>
								<td class="text-start">
									<a href="article.php?id=<?php echo $article["id"];?>">
										<?php echo $article["title"];?>
									</a>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			<?php } ?>

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


	<script>
		$(function () {
			$('[data-bs-toggle="tooltip"]').tooltip()
		});
	</script>

</body>
</html>
