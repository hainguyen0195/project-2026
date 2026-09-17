<div class="recruitment-heading" data-aos="fade-in" data-aos-duration="1000">
    <h1><?= (!empty($titleCate)) ? $titleCate : @$titleMain ?></h1>
</div>

<?php if (!empty($news)) {
    $featuredRecruitment = $news[0];
    $recruitmentItems = array_slice($news, 1, 6);
?>
    <section class="recruitment-featured" data-aos="fade-up" data-aos-duration="1000">
        <a class="recruitment-featured-image scale-img" href="<?= $featuredRecruitment[$sluglang] ?>" title="<?= $featuredRecruitment['name' . $lang] ?>">
            <img class="lazy" onerror="this.src='<?= THUMBS ?>/720x420x1/assets/images/noimage.png';" data-src="<?= THUMBS ?>/720x420x1/<?= UPLOAD_NEWS_L . $featuredRecruitment['photo'] ?>" alt="<?= $featuredRecruitment['name' . $lang] ?>" />
        </a>
        <div class="recruitment-featured-content">
            <span class="recruitment-label">Tuyển dụng</span>
            <h2><a href="<?= $featuredRecruitment[$sluglang] ?>" title="<?= $featuredRecruitment['name' . $lang] ?>"><?= $featuredRecruitment['name' . $lang] ?></a></h2>
            <div class="recruitment-featured-desc text-split"><?= $featuredRecruitment['desc' . $lang] ?></div>
            <a class="recruitment-view-detail" href="<?= $featuredRecruitment[$sluglang] ?>" title="<?= $featuredRecruitment['name' . $lang] ?>">Xem chi tiết <i class="fas fa-arrow-right"></i></a>
        </div>
    </section>

    <?php if (!empty($recruitmentItems)) { ?>
        <div class="recruitment-grid">
            <?php foreach ($recruitmentItems as $recruitment) { ?>
                <article class="recruitment-card" data-aos="fade-up" data-aos-duration="1000">
                    <a class="recruitment-card-image scale-img" href="<?= $recruitment[$sluglang] ?>" title="<?= $recruitment['name' . $lang] ?>">
                        <img class="lazy" onerror="this.src='<?= THUMBS ?>/430x260x1/assets/images/noimage.png';" data-src="<?= THUMBS ?>/430x260x1/<?= UPLOAD_NEWS_L . $recruitment['photo'] ?>" alt="<?= $recruitment['name' . $lang] ?>" />
                    </a>
                    <div class="recruitment-card-content">
                        <h3><a class="text-split" href="<?= $recruitment[$sluglang] ?>" title="<?= $recruitment['name' . $lang] ?>"><?= $recruitment['name' . $lang] ?></a></h3>
                        <div class="recruitment-card-desc text-split"><?= $recruitment['desc' . $lang] ?></div>
                    </div>
                </article>
            <?php } ?>
        </div>
    <?php } ?>
<?php } else { ?>
    <div class="alert alert-warning w-100" role="alert">
        <strong><?= khongtimthayketqua ?></strong>
    </div>
<?php } ?>

<?php if (!empty($paging)) { ?>
    <div class="pagination-home recruitment-pagination w-100"><?= $paging ?></div>
<?php } ?>
