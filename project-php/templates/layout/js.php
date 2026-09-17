<!-- Js Config -->
<script type="text/javascript">
    var NN_FRAMEWORK = NN_FRAMEWORK || {};
    var CONFIG_BASE = '<?= $configBase ?>';
    var ASSET = '<?= ASSET ?>';
    var WEBSITE_NAME = '<?= (!empty($setting['name' . $lang])) ? addslashes($setting['name' . $lang]) : '' ?>';
    var TIMENOW = '<?= date("d/m/Y", time()) ?>';
    var SHIP_CART = <?= (!empty($config['order']['ship'])) ? 'true' : 'false' ?>;
    var RECAPTCHA_ACTIVE = <?= (!empty($config['googleAPI']['recaptcha']['active'])) ? 'true' : 'false' ?>;
    var RECAPTCHA_SITEKEY = '<?= $config['googleAPI']['recaptcha']['sitekey'] ?>';
    var GOTOP = ASSET + 'assets/images/top.png';
    var LANG = {
        'no_keywords': '<?= chuanhaptukhoatimkiem ?>',
        'delete_product_from_cart': '<?= banmuonxoasanphamnay ?>',
        'no_products_in_cart': '<?= khongtontaisanphamtronggiohang ?>',
        'district': '<?= quanhuyen ?>',
        'ward': '<?= phuongxa ?>',
        'dangcapnhatdulieu': '<?= dangcapnhatdulieu ?>',
        'back_to_home': '<?= vetrangchu ?>',
        'thongbao': '<?= thongbao ?>',
        'dongy': '<?= dongy ?>',
        'dungluonghinhanhlon': '<?= dungluonghinhanhlon ?>',
        'dulieukhonghople': '<?= dulieukhonghople ?>',
        'banchiduocchon1hinhanhdeuplen': '<?= banchiduocchon1hinhanhdeuplen ?>',
        'dinhdanghinhanhkhonghople': '<?= dinhdanghinhanhkhonghople ?>',
        'huy': '<?= huy ?>',
    };
    var logo_img = '<?= $configBase ?><?= UPLOAD_PHOTO_L . $logo['photo'] ?>';
</script>

<!-- Js Files -->
<?php
$js->set("js/jquery.min.js");
$js->set("js/lazyload.min.js");
$js->set("bootstrap/bootstrap.js");
$js->set("js/wow.min.js");
$js->set("owlcarousel2/owl.carousel.js");
$js->set("slick/slick.js");
$js->set("holdon/HoldOn.js");
if ($source != 'index') {
    $js->set("confirm/confirm.js");
    $js->set("simplenotify/simple-notify.js");
    $js->set("easyticker/easy-ticker.js");
    $js->set("fotorama/fotorama.js");
    $js->set("photobox/photobox.js");
    $js->set("fileuploader/jquery.fileuploader.min.js");
    $js->set("datetimepicker/php-date-formatter.min.js");
    $js->set("datetimepicker/jquery.mousewheel.js");
    $js->set("datetimepicker/jquery.datetimepicker.js");
    $js->set("js/comment.js");
    $js->set("fancybox5/fancybox.umd.js");
    $js->set("magiczoomplus/magiczoomplus.js");
}
$js->set("js/functions.js");
$js->set("menu-mobile/menu-mobile.js");
$js->set("aos/aos.js");
$js->set("toc/toc.js");
$js->set("js/lenis.min.js");
$js->set("js/gsap.min.js");
$js->set("js/ScrollTrigger.min.js");
$js->set("js/splitting.min.js");
$js->set("js/about-cafune.js");
$js->set("js/apps.js");
echo $js->get();
?>
<?php if (!empty($config['googleAPI']['recaptcha']['active']) && $source == 'contact' && !$func->isRecaptchaSkip()) { ?>
    <!-- Js Google Recaptcha V3 -->
    <script src="https://www.google.com/recaptcha/api.js?render=<?= $config['googleAPI']['recaptcha']['sitekey'] ?>">
    </script>
    <?php /*
    <script type="text/javascript">
        grecaptcha.ready(function() {
            generateCaptcha('FormNewsletter','Newsletter', 'recaptchaResponseNewsletter');
        });
    </script>
    */ ?>
<?php } ?>

<?php if (!empty($config['oneSignal']['active'])) { ?>
    <!-- Js OneSignal -->
    <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
    <script type="text/javascript">
        var OneSignal = window.OneSignal || [];
        OneSignal.push(function() {
            OneSignal.init({
                appId: "<?= $config['oneSignal']['id'] ?>"
            });
        });
    </script>
<?php } ?>

<!-- Js Structdata -->
<?php include TEMPLATE . LAYOUT . "strucdata.php"; ?>

<?php if (!$func->isGoogleSpeed()) { ?>
    <!-- Js Addons -->
    <?= $addons->set('script-main', 'script-main', 2); ?>
    <?= $addons->get(); ?>

    <!-- Js Body -->
    <?= $func->decodeHtmlChars($setting['bodyjs']) ?>
<?php } ?>
