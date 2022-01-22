<?php
include("../includes/config.inc.php");
include("../lib/common/common-functions.inc.php");

$menuPoint = "articles";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

	<!-- JQUERY UI -->
	<link href="https://code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css" rel="stylesheet" type="text/css">
	
	<!-- JQUERY -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
	
	<!-- JQUERY UI -->
	<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.min.js" integrity="sha256-hlKLmzaRlE8SCJC1Kw8zoUbU8BxA+8kR3gseuKfMjxA=" crossorigin="anonymous"></script>
	
	<!-- FONT AWESOME ICONS  -->
	<link rel="stylesheet" href="/lib/fontawesome-pro-6.0.0-beta3-web/css/all.min.css" />
	
	<!-- TOASTR -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

	<link rel="stylesheet" type="text/css" href="/css/style.css?v=<?php echo defined("VERSION") ? VERSION : "1.0";?>">
	
</head>
<body>
    <main class="flex-shrink-0">
        <?php include("../includes/menu.inc.php"); ?>

        <div class="container-fluid">
            <?php if (isset($showDebug) && $showDebug && !empty($devMsgs)) { ?>
				<div class="alert alert-warning"><?php echo print_recursive($devMsgs);?></div>
			<?php } ?>

			<h1>Articles</h1>

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