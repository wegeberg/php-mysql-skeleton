<?php if (!isset($menuPoint)) $menuPoint = null; ?>

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-2">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo "/".ADM_PATH."articles.php";?>">
            <img src="/images/flexnet-256.png" alt="Flexnet" />
        </a>
        <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#menu-navbar" aria-controls="menu-navbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <?php 
            if (!defined("THE_FRONTPAGE") && defined("ADMIN_USER_ROLES")) { 
            // Don't show on login page
        ?>
            <div class="navbar-collapse collapse ps-3" id="menu-navbar">
                <ul class="navbar-nav mb-2 mb-lg-0 pt-1">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($menuPoint == "articles" ? 'active' : ''); ?>" href="<?php echo "/".ADM_PATH."articles.php";?>">
                            Articles
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a 
                            class="nav-link dropdown-toggle <?php echo ($menuPoint == "firstDropdownName" ? ' active' : ''); ?>" 
                            href="#" id="dropdown-1" 
                            role="button" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false"
                        >
                            Dropdown 1
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdown-1">
                            <li>
                                <a class="dropdown-item" href="#">
                                    First
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    Second
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto">
                    <li class="nav-item ml-5">
                        <a href="<?php echo "/".ADM_PATH."processLogin.php?logout=1";?>" title="Log out (<?php echo $_COOKIE[EMAIL_COOKIE_NAME]; ?>)" data-bs-toggle="tooltip" data-bs-placement="bottom" class="nav-link">
                            <i class="fal fa-sign-out-alt text-muted"></i>
                        </a>
                    </li>
                </ul>
            </div>
        <?php } ?>

    </div>
</nav>