<?php
$galleryHome = $d->rawQuery("select name$lang, photo from #_product where type = ? and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL) order by numb,id desc ", array('thu-vien-anh'));
if (!empty($galleryHome)) { ?>
    <div class="wrap-gallery py-4">
        <div class="container-custom">
            <div class="title-product text-center mb-4"><span>GALLERY</span></div>
            <div class="by-gallery-grid">
                <?php foreach ($galleryHome as $k => $v) { ?>
                    <a class="by-gallery-item by-gallery-item-<?= $k + 1 ?>" data-fancybox="gallery-home" href="<?= ASSET . UPLOAD_PRODUCT_L . $v['photo'] ?>" title="<?= $v['name' . $lang] ?>" <?php if ($k > 4) { ?> style="display:none" <?php } ?>>
                        <img class="lazy w-100 h-100 object-fit-cover" onerror="this.src='<?= THUMBS ?>/500x500x1/assets/images/noimage.png';" data-src="<?= ASSET . UPLOAD_PRODUCT_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>" />
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>