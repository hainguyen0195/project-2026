<?php
$tieuchi = $d->rawQuery("select name$lang, desc$lang, photo from #_news where type = ? and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac', status) = 0 OR status IS NULL) order by numb,id desc", array('tieu-chi'));

if (!empty($tieuchi)) { ?>
    <div class="wrap-tieuchi">
        <div class="container-custom">
            <div class="wrap-content">
                <div class="<?= (count($tieuchi) > 3) ? 'owl-page owl-carousel owl-theme' : 'tieuchi-list' ?>" <?= (count($tieuchi) > 3) ? 'data-items="screen:0|items:1|margin:15,screen:425|items:2|margin:15,screen:767|items:2|margin:15,screen:991|items:3|margin:15,screen:1199|items:3|margin:15" data-rewind="1" data-autoplay="1" data-loop="0" data-lazyload="0" data-mousedrag="1" data-touchdrag="1" data-smartspeed="500" data-autoplayspeed="3500" data-dots="0" data-nav="0"' : '' ?>>
                    <?php foreach ($tieuchi as $v) { ?>
                        <div class="tieuchi-item">
                            <div class="tieuchi-icon">
                                <img onerror="this.src='<?= THUMBS ?>/200x200x1/assets/images/noimage.png';" src="<?= THUMBS ?>/100x100x1/<?= UPLOAD_NEWS_L . $v['photo'] ?>" alt="<?= $v['name' . $lang] ?>" />
                            </div>
                            <div class="tieuchi-info">
                                <div class="tieuchi-name"><?= nl2br($v['name' . $lang]) ?></div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>