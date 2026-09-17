<?php
$testimonialHome = $d->rawQuery("select name$lang, desc$lang, photo from #_news where type = ? and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL) order by numb,id desc", array('khach-hang-noi-gi'));
if (!empty($testimonialHome)) { ?>
    <div class="wrap-khachhang py-5">
        <div class="container-custom">
            <div class="by-testi-header text-center mb-5">
                <div class="by-testi-icon mb-2">
                    <i class="fa-solid fa-users"></i>
                </div>

                <div class="title-product"><span>KHÁCH HÀNG TIN TƯỞNG</span></div>
                <div class="by-testi-stars">
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                </div>
            </div>
            <div class="by-testi-carousel owl-page owl-carousel owl-theme" data-items="screen:0|items:1|margin:20,screen:768|items:2|margin:30,screen:992|items:3|margin:40" data-rewind="1" data-autoplay="1" data-loop="0" data-lazyload="0" data-mousedrag="1" data-touchdrag="1" data-smartspeed="300" data-autoplayspeed="500" data-autoplaytimeout="3500" data-dots="1" data-nav="0">
                <?php foreach ($testimonialHome as $k => $v) { ?>
                    <div class="by-testi-item text-center">
                        <h3 class="by-testi-name"><?= $v['name' . $lang] ?></h3>
                        <div class="by-testi-avatar mx-auto">
                            <img class="lazy w-100 h-100 object-fit-cover" onerror="this.src='<?= THUMBS ?>/150x150x1/assets/images/noimage.png';" data-src="<?= THUMBS ?>/150x150x1/<?= UPLOAD_NEWS_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>" />
                        </div>
                        <div class="by-testi-desc">
                            <?= htmlspecialchars_decode($v['desc' . $lang] ?? '') ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>