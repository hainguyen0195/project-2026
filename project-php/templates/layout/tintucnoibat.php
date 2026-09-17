<?php
$newsHot = $d->rawQuery("select name$lang, slugvi, slugen, desc$lang, date_created, id, photo from #_news where type = ? and find_in_set('noibat',status) and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL) order by numb,id desc", array('blog'));

if (!empty($newsHot)) { ?>
    <section class="wrap-newsnb padding-top-bottom">
        <div class="container">
            <div class="newsnb-heading" data-aos="fade-in" data-aos-duration="1000">
                <span>Blog</span>
                <h2>Bí Quyết Chăm Sóc Da</h2>
            </div>
            <div class="newsnb-list<?= count($newsHot) > 3 ? ' newsnb-slick' : '' ?>">
                <?php foreach ($newsHot as $v) { ?>
                    <article class="item-newsnb" data-aos="fade-up" data-aos-duration="1000">
                        <p class="pic-newsnb">
                            <a class="scale-img" href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>">
                                <img class="lazy w-100" width="390" height="260" onerror="this.src='<?= THUMBS ?>/390x260x1/assets/images/noimage.png';" data-src="<?= !empty($v['photo']) ? THUMBS . '/390x260x1/' . UPLOAD_NEWS_L . $v['photo'] : 'assets/images/noimage.png' ?>" alt="<?= $v['name' . $lang] ?: $v['namevi'] ?>" title="<?= $v['name' . $lang] ?: $v['namevi'] ?>" />
                            </a>
                        </p>
                        <div class="info-newsnb">
                            <h3 class="mb-0 name-newsnb">
                                <a class="text-split" href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>"><?= $v['name' . $lang] ?></a>
                            </h3>
                            <div class="desc-newsnb text-split"><?= $v['desc' . $lang] ?></div>
                            <a class="newsnb-more" href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>">Xem Thêm <span aria-hidden="true">→</span></a>
                        </div>
                    </article>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>