<?php
$advertisingBanner = $d->rawQueryOne(
    "select name$lang, text1$lang, text2$lang, text3$lang, desc$lang, photo, status from #_static where type = ? limit 0,1",
    array('banner-quang-cao')
);

$advertisingBannerEnabled = !empty($advertisingBanner)
    && strpos((string)($advertisingBanner['status'] ?? ''), 'hienthi') !== false;

if ($advertisingBannerEnabled && !empty($advertisingBanner['photo'])) {
    $bannerOffer = trim($func->decodeHtmlChars($advertisingBanner['name' . $lang] ?? ''));
    $bannerTitle = trim($func->decodeHtmlChars($advertisingBanner['text1' . $lang] ?? ''));
    $bannerDescription = trim(strip_tags($func->decodeHtmlChars($advertisingBanner['desc' . $lang] ?? '')));
    $bannerButton = trim($func->decodeHtmlChars($advertisingBanner['text2' . $lang] ?? '')) ?: 'Tìm Hiểu Thêm';
    $bannerLink = trim($func->decodeHtmlChars($advertisingBanner['text3' . $lang] ?? '')) ?: '#';
    $bannerImage = UPLOAD_NEWS_L . $advertisingBanner['photo'];
?>
    <section class="home-advertising" aria-label="<?= htmlspecialchars($bannerTitle ?: $bannerOffer, ENT_QUOTES, 'UTF-8') ?>">
        <div class="home-advertising__inner" style="background-image:url('<?= htmlspecialchars($bannerImage, ENT_QUOTES, 'UTF-8') ?>')">
            <div class="home-advertising__content">
                <?php if ($bannerOffer !== '') { ?>
                    <p class="home-advertising__offer" data-aos="fade-up" data-aos-duration="700"><?= htmlspecialchars($bannerOffer, ENT_QUOTES, 'UTF-8') ?></p>
                <?php } ?>

                <?php if ($bannerTitle !== '') { ?>
                    <h2 class="home-advertising__title" data-aos="fade-up" data-aos-delay="120" data-aos-duration="700"><?= nl2br(htmlspecialchars($bannerTitle, ENT_QUOTES, 'UTF-8')) ?></h2>
                <?php } ?>

                <?php if ($bannerDescription !== '') { ?>
                    <p class="home-advertising__description" data-aos="fade-up" data-aos-delay="240" data-aos-duration="700"><?= nl2br(htmlspecialchars($bannerDescription, ENT_QUOTES, 'UTF-8')) ?></p>
                <?php } ?>

                <a class="home-advertising__button" data-aos="fade-up" data-aos-delay="360" data-aos-duration="700" href="<?= htmlspecialchars($bannerLink, ENT_QUOTES, 'UTF-8') ?>" title="<?= htmlspecialchars($bannerButton, ENT_QUOTES, 'UTF-8') ?>">
                    <span><?= htmlspecialchars($bannerButton, ENT_QUOTES, 'UTF-8') ?></span>
                    <i class="far fa-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </section>
<?php } ?>
