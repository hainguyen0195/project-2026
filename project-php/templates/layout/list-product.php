<?php
$proListHot = $d->rawQuery("select name$lang, slugvi, slugen, id,photo from #_product_list where type = ? and find_in_set('noibat',status) and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL) order by numb,id desc", array('san-pham'));
if (isset($proListHot) && !empty($proListHot) > 0) { ?>
    <?php foreach ($proListHot as $k => $v) {

    ?>
        <div class="section-product pad-bottom">
            <div class="container-custom">
                <div class="wrap-content">
                    <div class=" d-flex align-center justify-content-center flex-wrap d-title-choose-list ">
                        <div class="title-product">
                            <h2><?= $v['name' . $lang] ?></h2>
                        </div>
                    </div>

                    <div class="w-100">
                        <img class="w-100 lazy" onerror="this.src='<?= THUMBS ?>/1200x515x1/assets/images/noimage.png';" data-src="<?= THUMBS ?>/1200x515x1/<?= UPLOAD_PRODUCT_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>" />
                    </div>

                    <div class="wp_sp_index mt-5">
                        <div class="show_padding show_padding<?= $v['id'] ?>" data-list="<?= $v['id'] ?>" data-cat=""></div>
                    </div>

                    <?php if (!empty($bannerqc)) { ?>
                        <div class="container-custom">
                            <div class="wrap-content">

                                <a href="<?= $bannerqc['link'] ?>" target="_blank" class="by-catalog-click" title="<?= $bannerqc['name' . $lang] ?>">
                                    <img onerror="this.src='<?= THUMBS ?>/1200x170x1/assets/images/noimage.png';" src="<?= THUMBS ?>/1200x170x1/<?= UPLOAD_PHOTO_L . $bannerqc['photo'] ?>" alt="<?= $bannerqc['name' . $lang] ?>" title="<?= $bannerqc['name' . $lang] ?>" />
                                </a>

                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
<?php } ?>