<!DOCTYPE html>
<html lang="<?= $config['website']['lang-doc'] ?>">

<head>
    <?php include TEMPLATE . LAYOUT . "head.php"; ?>
    <?php include TEMPLATE . LAYOUT . "css.php"; ?>
</head>

<body>
    <div class="conttt">
        <?php
        //include TEMPLATE . LAYOUT . "loader-wrapper.php";
        include TEMPLATE . LAYOUT . "seo.php";
        include TEMPLATE . LAYOUT . "header.php";
        // include TEMPLATE . LAYOUT . "mmenu.php";
        if ($source == 'index') include TEMPLATE . LAYOUT . "slide.php";
        else include TEMPLATE . LAYOUT . "breadcrumb.php";
        ?>
        <?php if ($source == 'index') { ?>
            <main id="main-content" class="main page-home wrap-home"><?php include TEMPLATE . $template . "_tpl.php"; ?></main>
        <?php } else { ?>
            <main id="main-content" class="container">
                <div class="<?= $source == 'about' ? 'wrap-about' : 'wrap-main-new ' ?>">
                    <?php include TEMPLATE . $template . "_tpl.php"; ?>
                </div>
            </main>
        <?php } ?>
        <?php
        include TEMPLATE . LAYOUT . "footer.php";
        include TEMPLATE . LAYOUT . "social.php";
        include TEMPLATE . LAYOUT . "modal.php";
        include TEMPLATE . LAYOUT . "js.php";
        include TEMPLATE . LAYOUT . "phone.php";
        ?>
    </div>
</body>

</html>