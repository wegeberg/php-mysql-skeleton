<?php
include("./script-constants.inc.php");
if(!isset($db)) {
	$db = new db(DB_NAME);
}
$showDebug = false;
$devMsgs = [];
$status = null;

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
if(!$id) {
    die("id mangler");
}
$artikel = $db->get_row($backup_tabel, $id);
$devMsgs[] = $db->sql;
if(empty($artikel)) {
    die("Artikel ikke fundet");
}
?>
<!DOCTYPE html>
<html lang="da">
<head>
<meta charSet="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1.0, maximum-scale=1, user-scalable=no" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

    <title>Backup preview</title>
    <style>
        .ephox-summary-card {
            font-size: 0.9rem;
            border: 1px solid #cccccc;
            border-radius: 12px;
            padding: 5px;
        }
        a.ephox-summary-card-link {
            color: rgb(29, 161, 242);
        }
        .ephox-summary-card-title {
            color: black;
            font-size: 1.0rem;
            font-weight: 700;
            display: block;
        }
        .ephox-summary-card-author, .ephox-summary-card-website {
            color: black;
            display: block;
        }
        }
    </style>
</head>
<body>
    <div id="fb-root"></div>
    <script async defer src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2"></script>
    <div class="container pt-4">

        <div class="row mb-4">
            <div class="col text-center">
                <h3>
                    Backup gemt <?php echo date("d/m-Y H.i", strtotime($artikel["created_at"]));?>
                </h3>
            </div>
        </div>

        <div class="row mt-5 pt-4">
            <div class="col-12 col-lg-9">

                <!-- TROMPET -->
                <?php if($artikel["trompet"]) { ?>
                    <h4 class="trompet">
                        <?php echo $artikel["trompet"];?>:
                    </h4>
                <?php } ?>

                <!-- RUBRIK -->
                <h2>
                    <?php echo $artikel["rubrik"];?>
                </h2>

                <!-- DATO -->
                <div class="row pt-2 pb-0">
                    <div class="col-12 text-muted">
                        <?php
                            echo date("d/m-Y h.i", strtotime($artikel["publicering"]));
                        ?>
                    </div>
                </div>

                <!-- BYLINE -->
                <?php if(isset($artikel["forfatter"])) { ?>
                    <div class="row udstyr pb-4 pt-2 mb-4 border-bottom">
                        <div class="col-12 text-muted byline">
                            <div class="byline clearfix">
                                <div class="float-left">
                                    <span class="text-strong">
                                        <?php echo $artikel["forfatter"];?>
                                    </span>
                                    <?php echo $artikel["forfatter_email"] ? "<br />{$artikel["forfatter_email"]}" : "";?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- / .row -->
                <?php } ?>

                <!-- MANCHET -->
                <?php if($artikel["manchet"] && strlen(trim($artikel["manchet"])) > 0) { ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="manchet">
                                <?php echo $artikel["manchet"];?>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <!-- BRØD -->
                <div class="row" id="broed">
                    <div class="col-12 articleBody">
                        <article>
                            <?php echo $artikel["broed"];?>
                        </article>
                    </div>
                </div>

            </div>
            <!-- / col-12 col-lg-9 -->
        </div>
        <!-- / .row -->
    </div>
    <!-- / .container -->
    <?php
    if($showDebug) {
        echo implode("<br />", $devMsgs);
    }
    ?>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script>
        window.twttr = (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0],
            t = window.twttr || {};
        if (d.getElementById(id)) return t;
        js = d.createElement(s);
        js.id = id;
        js.src = "https://platform.twitter.com/widgets.js";
        fjs.parentNode.insertBefore(js, fjs);

        t._e = [];
        t.ready = function(f) {
            t._e.push(f);
        };

        return t;
        }(document, "script", "twitter-wjs"));
    </script>
</body>
</html>