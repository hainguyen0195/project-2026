<?php
$bannerqc2 = $cache->get("select name$lang, desc$lang, photo, link from #_photo where type = ? and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL) order by numb,id desc", array('qc2'), 'result', 7200);
if (!empty($bannerqc2)) { ?>
    <div class="wrap-brand padding-top-bottom">
        <div class="container-custom">
            <div class="wrap-content">
                <div class="owl-page owl-carousel owl-theme" data-items="screen:0|items:1|margin:10,screen:425|items:31|margin:10,screen:575|items:1|margin:10,screen:767|items:1|margin:10,screen:991|items:1|margin:10,screen:1199|items:1|margin:10" data-rewind="1" data-autoplay="1" data-loop="0" data-lazyload="0" data-mousedrag="1" data-touchdrag="1" data-smartspeed="500" data-autoplayspeed="3500" data-dots="0" data-nav="1" data-navcontainer=".control-brand">
                    <?php foreach ($bannerqc2 as $v) { ?>
                        <div>
                            <a class=" text-decoration-none" href="<?= $v["link"] ?>" title="<?= $v['name' . $lang] ?>">
                                <img class="w-100 lazy" onerror="this.src='<?= THUMBS ?>/1400x345x1/assets/images/noimage.png';" data-src="<?= THUMBS ?>/1200x145x1/<?= UPLOAD_PHOTO_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>" />
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>