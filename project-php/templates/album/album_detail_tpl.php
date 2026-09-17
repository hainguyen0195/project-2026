<div class="title-main"><span><?= $rowDetail['name' . $lang] ?></span></div>
<div class="content-main">
    <?php if (isset($rowDetailPhoto) && count($rowDetailPhoto) > 0) { ?>
        <div class="row-album row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4">
            <?php foreach ($rowDetailPhoto as $k => $v) { ?>
                <div class="col col-album">
                    <div class="album" data-aos="fade-up" data-aos-duration="1000">
                        <a class="album-image scale-img mb-0" data-fancybox="gallery" data-src="<?= ASSET . UPLOAD_PRODUCT_L . $v['photo'] ?>" data-caption="" title="<?= $rowDetail['name' . $lang] ?>">
                            <img class="lazy w-100" onerror="this.src='<?= THUMBS ?>/480x360x1/assets/images/noimage.png';" data-src="<?= THUMBS ?>/480x360x1/<?= UPLOAD_PRODUCT_L . $v['photo'] ?>" alt="<?= $rowDetail['name' . $lang] ?>" title="<?= $rowDetail['name' . $lang] ?>" />
                        </a>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>