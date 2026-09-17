<!-- Css Files -->
<?php
$css->set("bootstrap/bootstrap.css");
$css->set("menu-mobile/menu-mobile.css");
$css->set("slick/slick.css");
$css->set("slick/slick-theme.css");
$css->set("slick/slick-style.css");
$css->set("owlcarousel2/owl.carousel.css");
$css->set("owlcarousel2/owl.theme.default.css");
$css->set("holdon/HoldOn.css");
$css->set("holdon/HoldOn-style.css");
if ($source != 'index') {
    $css->set("bootstrap/bootstrap-icons.css");
    $css->set("confirm/confirm.css");
    $css->set("fileuploader/font-fileuploader.css");
    $css->set("fileuploader/jquery.fileuploader.min.css");
    $css->set("fileuploader/jquery.fileuploader-theme-dragdrop.css");
    $css->set("datetimepicker/jquery.datetimepicker.css");
    $css->set("photobox/photobox.css");
    $css->set("fotorama/fotorama.css");
    $css->set("fotorama/fotorama-style.css");
    $css->set("simplenotify/simple-notify.css");
    $css->set("fancybox5/fancybox.css");
    $css->set("magiczoomplus/magiczoomplus.css");
}
$css->set("css/style.css");

$css->set("css/styleduan.css");
$css->set("css/style-media.css");
$css->set("css/header-hd.css");
$css->set("css/about-cafune.css");
echo $css->get();
?>
<link rel="preload" href="<?= ASSET ?>assets/css/animate.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
<link rel="preload" href="<?= ASSET ?>assets/aos/aos.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
<script>
    (() => {
        let loaded = false;
        const loadIcons = () => {
            if (loaded) return;
            loaded = true;
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = '<?= ASSET ?>assets/fontawesome640/all.css';
            document.head.appendChild(link);
        };
        ['pointerdown', 'keydown', 'touchstart', 'scroll'].forEach((eventName) => {
            window.addEventListener(eventName, loadIcons, {
                once: true,
                passive: true
            });
        });
        window.addEventListener('load', () => window.setTimeout(loadIcons, 7000), {
            once: true
        });
    })();
</script>
<noscript>
    <link rel="stylesheet" href="<?= ASSET ?>assets/fontawesome640/all.css">
    <link rel="stylesheet" href="<?= ASSET ?>assets/css/animate.min.css">
    <link rel="stylesheet" href="<?= ASSET ?>assets/aos/aos.css">
</noscript>

<!-- Background -->
<?php
if (empty($config['background']) && defined('LIBRARIES') && is_file(LIBRARIES . 'type/config-type-background.php')) {
    if (!defined('hienthi')) define('hienthi', 'Hiển thị');
    require_once LIBRARIES . 'type/config-type-background.php';
}

if (!empty($config['background'])) {
    $bgTypes = array_keys($config['background']);
    $bgPlaceholders = implode(',', array_fill(0, count($bgTypes), '?'));
    $bgRows = $d->rawQuery(
        "select type, status, options, photo from #_photo where act = ? and type in ($bgPlaceholders)",
        array_merge(array('photo_static'), $bgTypes)
    );
    $bgByType = array();
    if (!empty($bgRows)) {
        foreach ($bgRows as $bgRow) {
            $bgByType[$bgRow['type']] = $bgRow;
        }
    }

    $bgCss = '';
    foreach ($config['background'] as $bgType => $bgConfig) {
        if (empty($bgByType[$bgType])) continue;
        $bgItem = $bgByType[$bgType];
        if (empty($bgItem['status']) || strpos($bgItem['status'], 'hienthi') === false) continue;

        $bgOptsAll = !empty($bgItem['options']) ? json_decode($bgItem['options'], true) : array();
        $bgOpts = !empty($bgOptsAll['background']) && is_array($bgOptsAll['background']) ? $bgOptsAll['background'] : array();
        $selector = !empty($bgOpts['selector']) ? trim(html_entity_decode($bgOpts['selector'], ENT_QUOTES, 'UTF-8')) : '';
        if ($selector === '' && !empty($bgConfig['selector'])) $selector = trim($bgConfig['selector']);
        if ($selector === '' || $selector === '.header' || $selector === '#header') {
            $selector = !empty($bgConfig['selector']) ? trim($bgConfig['selector']) : 'body';
        }
        $selector = preg_replace('/[^a-zA-Z0-9\s.#,_:\->\[\]="*]+/', '', $selector);
        if ($selector === '') continue;

        if (!empty($bgOpts['type_show'])) {
            if (empty($bgItem['photo'])) continue;
            $repeat = !empty($bgOpts['repeat']) ? $bgOpts['repeat'] : 'no-repeat';
            $position = !empty($bgOpts['position']) ? $bgOpts['position'] : 'center top';
            $attachment = (!empty($bgOpts['attachment']) && $bgOpts['attachment'] !== '0') ? $bgOpts['attachment'] : '';
            $size = !empty($bgOpts['size']) ? $bgOpts['size'] : 'cover';
            $bgCss .= $selector . '{background:url(' . UPLOAD_PHOTO_L . $bgItem['photo'] . ') ' . $repeat . ' ' . $position . ' ' . $attachment . ' !important;background-size:' . $size . ' !important}';
        } else {
            $color = !empty($bgOpts['color']) ? ltrim($bgOpts['color'], '#') : '';
            if ($color === '' || !preg_match('/^[0-9a-fA-F]{3,8}$/', $color)) continue;
            $bgCss .= $selector . '{ background-color:#' . $color . ' !important}';
        }
    }

    if ($bgCss !== '') {
        echo '<style type="text/css">' . $bgCss . '</style>';
    }
}
?>

<!-- Js Google Analytic -->
<?php if (!$func->isGoogleSpeed()) { ?>
    <?php
    $analyticsMarkup = $func->decodeHtmlChars($setting['analytics']);
    preg_match_all('/G-[A-Z0-9]+/i', $analyticsMarkup, $analyticsMatches);
    $analyticsIds = array_values(array_unique($analyticsMatches[0] ?? []));
    ?>
    <?php if (!empty($analyticsIds)) { ?>
        <script>
            (() => {
                const measurementIds = <?= json_encode($analyticsIds, JSON_UNESCAPED_SLASHES) ?>;
                let loaded = false;
                const loadAnalytics = () => {
                    if (loaded) return;
                    loaded = true;
                    window.dataLayer = window.dataLayer || [];
                    window.gtag = window.gtag || function() {
                        window.dataLayer.push(arguments);
                    };
                    window.gtag('js', new Date());
                    measurementIds.forEach((id) => window.gtag('config', id));
                    const script = document.createElement('script');
                    script.async = true;
                    script.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(measurementIds[0]);
                    document.head.appendChild(script);
                };
                ['pointerdown', 'keydown', 'touchstart', 'scroll'].forEach((eventName) => {
                    window.addEventListener(eventName, loadAnalytics, {
                        once: true,
                        passive: true
                    });
                });
                window.addEventListener('load', () => window.setTimeout(loadAnalytics, 15000), {
                    once: true
                });
            })();
        </script>
    <?php } ?>
<?php } ?>

<!-- Js Head -->
<?php if (!$func->isGoogleSpeed()) { ?>
    <?= $func->decodeHtmlChars($setting['headjs']) ?>
<?php } ?>
