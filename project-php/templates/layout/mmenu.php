<nav id="menu">
    <div class="menu-mobile-head">
        <span class="menu-mobile-head__space" aria-hidden="true"></span>
        <a class="menu-mobile-head__logo" href="" title="<?= trangchu ?>">
            <img src="<?= $logoSrc ?>" alt="<?= $brandName ?>">
        </a>
        <div class="menu-mobile-head__tools">
            <button class="menu-mobile-search-open hd-open-search" type="button" aria-label="<?= timkiem ?>">
                <img src="assets/images/hd/icon-search.svg" alt="">
            </button>
            <a href="gio-hang" aria-label="<?= giohang ?>" class="menu-mobile-cart">
                <img src="assets/images/hd/icon-cart.svg" alt="">
                <span class="count-cart"><?= (int)$cartQty ?></span>
            </a>
        </div>
    </div>
    <ul>
        <li><a href="" class="<?= $com == '' ? 'active' : '' ?>" title="<?= trangchu ?>"><?= trangchu ?></a></li>
        <li><a href="gioi-thieu" class="<?= $com == 'gioi-thieu' ? 'active' : '' ?>" title="<?= gioithieu ?>"><?= gioithieu ?></a></li>
        <li>
            <a href="san-pham" class="<?= $com == 'san-pham' ? 'active' : '' ?>" title="<?= sanpham ?>">
                <?= sanpham ?>
            </a>
            <?php if (!empty($productListMenu) && count($productListMenu) > 0) { ?>
                <ul>
                    <?php foreach ($productListMenu as $v) {
                        $spcatMenu = $d->rawQuery("select name$lang, slugvi, slugen, id from #_product_cat where id_list = ? and find_in_set('hienthi',status) order by numb,id desc", array($v['id']));
                    ?>
                        <li>
                            <a href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>"><?= $v['name' . $lang] ?></a>
                            <?php if (!empty($spcatMenu) && count($spcatMenu) > 0) { ?>
                                <ul>
                                    <?php foreach ($spcatMenu as $c) {
                                        $spitemMenu = $d->rawQuery("select name$lang, slugvi, slugen, id from #_product_item where id_cat = ? and find_in_set('hienthi',status) order by numb,id desc", array($c['id']));
                                    ?>
                                        <li>
                                            <a href="<?= $c[$sluglang] ?>" title="<?= $c['name' . $lang] ?>"><?= $c['name' . $lang] ?></a>
                                            <?php if (!empty($spitemMenu) && count($spitemMenu) > 0) { ?>
                                                <ul>
                                                    <?php foreach ($spitemMenu as $i) { ?>
                                                        <li>
                                                            <a href="<?= $i[$sluglang] ?>" title="<?= $i['name' . $lang] ?>"><?= $i['name' . $lang] ?></a>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            <?php } ?>
                                        </li>
                                    <?php } ?>
                                </ul>
                            <?php } ?>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </li>
        <li class="menu-duan-item">
            <a href="du-an" class="<?= $com == 'du-an' ? 'active' : '' ?>" title="<?= duan ?>"><?= duan ?></a>
            <?php if (!empty($duanListMenu) && count($duanListMenu) > 0) { ?>
                <ul>
                    <?php foreach ($duanListMenu as $v) {
                    ?>
                        <li>
                            <a href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>"><?= $v['name' . $lang] ?></a>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </li>
        <li><a href="tuyen-dung" class="<?= $com == 'tuyen-dung' ? 'active' : '' ?>" title="<?= tuyendung ?>"><?= tuyendung ?></a></li>
        <li><a href="tin-tuc" class="<?= $com == 'tin-tuc' ? 'active' : '' ?>" title="<?= tintuc ?>"><?= tintuc ?></a></li>
        <li><a href="lien-he" class="<?= $com == 'lien-he' ? 'active' : '' ?>" title="<?= lienhe ?>"><?= lienhe ?></a></li>
    </ul>

    <div class="menu-mobile-contact">
        <?php if (!empty($optsetting['email'])) { ?>
            <div><strong>Email:</strong><a href="mailto:<?= htmlspecialchars($optsetting['email'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($optsetting['email'], ENT_QUOTES, 'UTF-8') ?></a></div>
        <?php } ?>
        <?php if (!empty($optsetting['hotline'])) { ?>
            <div><strong><?= dienthoai ?>:</strong><a href="tel:<?= htmlspecialchars($func->parsePhone($optsetting['hotline']), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($optsetting['hotline'], ENT_QUOTES, 'UTF-8') ?></a></div>
        <?php } ?>
        <?php if (!empty($optsetting['address'])) { ?>
            <div><strong><?= diachi ?>:</strong><span><?= htmlspecialchars($optsetting['address'], ENT_QUOTES, 'UTF-8') ?></span></div>
        <?php } ?>
    </div>
</nav>
