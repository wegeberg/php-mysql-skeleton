<?php
include("../includes/constants.inc.php");
include("../lib/common/common-functions.inc.php");

const THE_FRONTPAGE = true;

// Already logged in?
if (ADMIN_USER_ID > 0 && array_intersect(ADMIN_USER_ROLES, ACCESS_ROLES)) {
    header("Location: " . ADM_PATH . "articles.php");
}

// If request for sub page - redirect to this after login
$targetUrl = isset($_GET["url"]) && $_GET["url"] ? $_GET["url"] : "";
if (isset($_GET["err"])) {
    $msgError = urldecode($_GET["err"]);
}

$userEmail = $_COOKIE[EMAIL_COOKIE_NAME] ?? "";

if (isset($_GET["err"])) {
    $msgError = urldecode($_GET["err"]);
}
?>
<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Flexnet"/>
    <meta name="author" content="Martin Wegeberg"/>
    <title>Flexnet CMS</title>

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            crossorigin="anonymous"></script>

    <!-- JQUERY UI -->
    <link href="https://code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css" rel="stylesheet" type="text/css">

    <!-- JQUERY -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- JQUERY UI -->
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js" crossorigin="anonymous"></script>
    <link href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" rel="stylesheet" type="text/css">

    <!-- FONT AWESOME ICONS  -->
    <link rel="stylesheet" href="/lib/fontawesome-free-6.7.2-web/css/all.css"/>

    <!-- TOASTR -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <link rel="stylesheet" type="text/css" href="/css/style.css?v=<?php echo defined("VERSION") ? VERSION : "1.0"; ?>">

</head>
<body>
<main class="flex-shrink-0">
    <?php include("../includes/menu.inc.php"); ?>
    <div class="container-fluid">
        <?php if (isset($showDebug) && $showDebug && !empty($devMsgs)) { ?>
            <div class="alert alert-warning"><?php print_recursive($devMsgs); ?></div>
        <?php } ?>

        <div class="row">
            <div class="mx-auto col-12 col-md-4">
                <div class="card my-5">
                    <div class="card-header text-center">
                        Login - Flexnet CMS
                    </div>
                    <div class="card-body">
                        <form
                                role="form"
                                action="<?php echo "/" . ADM_PATH . "processLogin.php"; ?>"
                                accept-charset="UTF-8"
                                method="post"
                                target="_self"
                                enctype="multipart/form-data"
                        >
                            <input
                                    type="hidden"
                                    value="<?php echo $targetUrl; ?>"
                                    id="url"
                                    name="url"
                            >
                            <div class="mb-3">
                                <?php echo input_field([
                                    "name" => "email",
                                    "type" => "email",
                                    "required" => true,
                                    "noLabel" => true,
                                    "value" => $userEmail
                                ]); ?>
                            </div>
                            <div class="mb-3">
                                <?php echo input_field([
                                    "name" => "password",
                                    "type" => "password",
                                    "required" => true,
                                    "noLabel" => true,
                                    "extras" => ["autofocus" => "off"]
                                ]); ?>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-block">
                                    Login
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>

<!-- TOASTR -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- COMMON FUNCTIONS -->
<script src="/lib/common/js/common.js?v=<?php echo defined("VERSION") ? VERSION : "1.0"; ?>"></script>

<script>
    <?php if (isset($msgSuccess) && $msgSuccess) { ?>
    toastr.success('<?php echo $msgSuccess;?>');
    <?php } ?>
    <?php if (isset($msgError) && $msgError) { ?>
    toastr.error('<?php echo $msgError;?>');
    <?php } ?>
</script>

</body>
</html>
