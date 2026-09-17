<?php
$videoHot = $d->rawQuery("select id,name$lang, link_video from #_photo where type = ? and find_in_set('noibat',status) and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL)", array('video'));

if (!empty($videoHot)) { ?>
    <div class="wrap-intro py-4">
        <div class="container-custom">
            <div class="wrap-content ">

                <div class="">
                    <div class="title-product"><span>Video Clip</span></div>
                    <?php if (!empty($videoHot)) { ?>
                        <div class="videohome-intro">

                            <div class="div_hiden">
                                <div class="owl-page owl-carousel owl-theme owl-video" data-items="screen:0|items:1|margin:10,screen:425|items:2|margin:10,screen:575|items:2|margin:10,screen:767|items:2|margin:10,screen:991|items:2|margin:10,screen:1199|items:2|margin:10" data-rewind="1" data-autoplay="1" data-loop="0" data-lazyload="0" data-mousedrag="1" data-touchdrag="1" data-smartspeed="300" data-autoplayspeed="500" data-autoplaytimeout="3500" data-dots="0" data-nav="1" data-navcontainer=".control-video">
                                    <?php foreach ($videoHot as $k => $v) { ?>
                                        <div>
                                            <a class="item-video2 pic-video-2 text-decoration-none " data-fancybox="video-gallery" href="<?= $v['link_video'] ?>" title="<?= $v['name' . $lang] ?>">
                                                <img onerror="this.src='<?= THUMBS ?>/540x500x2/assets/images/noimage.png';" src="https://img.youtube.com/vi/<?= $func->getYoutube($v['link_video']) ?>/0.jpg" alt="<?= $v['name' . $lang] ?>" />
                                                <div class="name-video">
                                                    <p><?= $v['name' . $lang] ?></p>
                                                </div>

                                            </a>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php //echo $addons->set('video-fotorama', 'video-fotorama', 4); 
                            ?>
                            <?php //echo $addons->set('video-select', 'video-select', 4); 
                            ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>