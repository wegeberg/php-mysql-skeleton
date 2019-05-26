<?php
require_once("./includes/constants.inc.php");
?>
<!DOCTYPE html>
<html lang="da">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Flexnet" />
    <meta name="author" content="Wegeberg" />
    <title>Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link href="lib/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet" type="text/css">
    <link href="css/style.css?v=<?php echo VERSION; ?>" rel="stylesheet" type="text/css">
    <link rel="apple-touch-icon" sizes="180x180" href="ikoner/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="ikoner/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="ikoner/favicon-16x16.png">
    <link rel="manifest" href="ikoner/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <link rel="stylesheet" href="css/fontawesome-pro-5.6.3-web/css/all.min.css" />
</head>
<body>
<main class="flex-shrink-0">
        <?php include("includes/menuer/menu.inc.php"); ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="content-wrapper">
                        <?php if (!empty($errMsgs)) { ?>
                        <div class="row">
                            <div class="col">
                                <div class="alert alert-warning">
                                    <?php echo implode("<br />", $errMsgs); ?>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        <?php if ($showDebug && !empty($devMsgs)) { ?>
                        <div class="row">
                            <div class="col">
                                <div class="alert alert-info">
                                    <?php echo implode("<br />", $devMsgs); ?>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        <?php if (!empty($statusMsgs)) { ?>
                        <div class="row" id="statusRow">
                            <div class="col">
                                <div class="alert alert-success">
                                    <?php echo implode("<br />", $statusMsgs); ?>
                                </div>
                            </div>
                        </div>
                        <?php } ?>

                        <div class="row p-2 mb-2">
                            <div class="col-12">
                                <p>
                                    Indhold
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <footer class="footer bg-light">
        <div class="container">
            <span class="text-muted">Martin Wegeberg &bull; Flexnet 2019
                <?php echo intval(date("Y")) !== 2019 ? "-" . date("Y") : ""; ?>
            </span>
        </div>
    </footer>

    <script type="text/javascript" src="lib/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="lib/jquery-ui-1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>