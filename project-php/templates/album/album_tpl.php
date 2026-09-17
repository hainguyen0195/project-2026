<div class="title-main"><span><?= $titleMain ?></span></div>
<?php if (isset($product) && count($product) > 0) { ?>
    <div class="row-album row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4">
        <?php foreach ($product as $k => $v) { ?>
            <div class="col col-album">
                <div class="album" data-aos="fade-up" data-aos-duration="1000">
                    <a class="pic-album cale-img" href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>">
                        <img class="lazy w-100" onerror="this.src='<?= THUMBS ?>/285x285x1/assets/images/noimage.png';" data-src="<?= THUMBS ?>/285x285x1/<?= UPLOAD_PRODUCT_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>" title="<?= $v['name' . $lang] ?>" />
                    </a>
                    <h3 class="name-album">
                        <a class="text-split" href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>"><?= $v['name' . $lang] ?></a>
                    </h3>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="pagination-home w-100"><?= (!empty($paging)) ? $paging : '' ?></div>
<?php } else { ?>
    <div class="alert alert-warning" role="alert">
        <strong><?= khongtimthayketqua ?></strong>
    </div>
<?php } ?>