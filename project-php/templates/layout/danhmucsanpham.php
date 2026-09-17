<?php
$productListNB = $d->rawQuery("select id, name$lang, slugvi, slugen, photo from #_product_list where type = ? and find_in_set('noibat',status) and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL) order by numb,id desc", array('san-pham'));
if (!empty($productListNB)) { ?>
    <section class="sec-hbs ss-pd-b" aria-label="<?= htmlspecialchars(danhmucsanpham, ENT_QUOTES, 'UTF-8') ?>">
        <div class="container">
            <div class="hbs hbs-staggered">
                <div class="hbs-wrap">
                    <div class="hbs-slider hbs-slider--staggered">
                        <?php foreach ($productListNB as $index => $v) {
                            $name = trim((string)($v['name' . $lang] ?? ''));
                            $url = !empty($v[$sluglang]) ? $v[$sluglang] : '#';
                            $cardClass = ($index % 2 === 0) ? 'is-content-top' : 'is-image-top';
                        ?>
                            <div class="hbs-col <?= $cardClass ?>">
                                <article class="bcate-it">
                                    <div class="b-ctn">
                                        <span class="bcate-brand">COMI VIET NAM</span>
                                        <h3 class="t-link"><a href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></a></h3>
                                        <a class="bcate-arrow" href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>" aria-label="Xem <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>">
                                            <i class="far fa-arrow-right" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                    <div class="b-image">
                                        <a class="inner" href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>">
                                            <img
                                                src="<?= THUMBS ?>/800x800x1/<?= UPLOAD_PRODUCT_L . $v['photo'] ?>"
                                                onerror="this.src='<?= THUMBS ?>/800x800x2/assets/images/noimage.png';"
                                                alt="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>"
                                                width="800" height="800"
                                                <?= $index > 3 ? 'loading="lazy"' : '' ?>>
                                        </a>
                                    </div>
                                </article>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } ?>
