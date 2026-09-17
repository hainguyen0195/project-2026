<?php include TEMPLATE . 'product/product_detail_overview.php'; ?>
<?php if (false) { ?>
<div class="grid-pro-detail d-flex flex-wrap justify-content-between align-items-start">
    <div class="left-pro-detail">
        <a id="Zoom-1" class="MagicZoom" data-options="zoomMode: off; hint: off; rightClick: true; selectorTrigger: hover; expandCaption: false; history: false;" href="<?= THUMBS ?>/1040x1040x1/<?= UPLOAD_PRODUCT_L . $rowDetail['photo'] ?>" title="<?= $rowDetail['name' . $lang] ?>">
            <img class="w-100" src="<?= THUMBS ?>/1040x1040x1/<?= UPLOAD_PRODUCT_L . $rowDetail['photo'] ?>" alt="<?= $rowDetail['name' . $lang] ?>" title="<?= $rowDetail['name' . $lang] ?>" />
        </a>
    </div>
    <div class="right-pro-detail">
        <p class="title-pro-detail mb-2"><?= $rowDetail['name' . $lang] ?></p>
        <?php if (empty($quickview)) { ?>
            <div class="social-plugin social-plugin-pro-detail w-clear">
                <?php
                $params = array();
                $params['oaid'] = $optsetting['oaidzalo'];
                echo $func->markdown('social/share', $params);
                ?>
            </div>
        <?php } ?>
        <ul class="attr-pro-detail">
            <li class="w-clear attr-pro-detail-price">
                <label class="attr-label-pro-detail attr-label-pro-detail-price"><?= gia ?>:</label>
                <div class="attr-content-pro-detail">
                    <?php if ($rowDetail['sale_price'] > 0) { ?>
                        <span class="price-new-pro-detail"><?= $func->formatMoney($rowDetail['sale_price']) ?></span>
                        <span class="price-old-pro-detail"><?= $func->formatMoney($rowDetail['regular_price']) ?></span>
                    <?php } else { ?>
                        <span class="price-new-pro-detail"><?= ($rowDetail['regular_price']) ? $func->formatMoney($rowDetail['regular_price']) : lienhe ?></span>
                    <?php } ?>
                </div>
            </li>
        </ul>
        <?php if (empty($quickview)) { ?>
            <div class="desc-pro-detail content-ck"><?= $func->autoLinkKeyword($func->getHtmlChars($rowDetail['desc' . $lang])) ?></div>
        <?php } ?>
        <ul class="attr-pro-detail">
            <?php if (!empty($rowDetail['code'])) { ?>
                <li>
                    <label class="attr-label-pro-detail"><?= masp ?>:</label>
                    <div class="attr-content-pro-detail"><?= $rowDetail['code'] ?></div>
                </li>
            <?php } ?>
            <li>
                <label class="attr-label-pro-detail"><?= luotxem ?>:</label>
                <div class="attr-content-pro-detail"><?= $rowDetail['view'] ?></div>
            </li>
            <?php if (!empty($productBrand['id'])) { ?>
                <li class="w-clear">
                    <label class="attr-label-pro-detail"><?= thuonghieu ?>:</label>
                    <div class="attr-content-pro-detail brand-pro-detail">
                        <a class="text-decoration-none" href="<?= $productBrand[$sluglang] ?>" title="<?= $productBrand['name' . $lang] ?>"><?= $productBrand['name' . $lang] ?></a>
                    </div>
                </li>
            <?php } ?>
            <?php if (!empty($rowColor)) { ?>
                <li class="color-block-pro-detail w-clear">
                    <label class="attr-label-pro-detail d-block"><?= mausac ?>:</label>
                    <div class="attr-content-pro-detail d-flex flex-wrap">
                        <?php foreach ($rowColor as $k => $v) { ?>
                            <?php if ($v['type_show'] == 1) { ?>
                                <label for="color-pro-detail-<?= $v['id'] ?>" class="color-pro-detail text-decoration-none <?= ($k == 0) ? "active" : "" ?>" data-idproduct="<?= $rowDetail['id'] ?>" style="background-image: url(<?= UPLOAD_COLOR_L . $v['photo'] ?>)">
                                    <input type="radio" value="<?= $v['id'] ?>" id="color-pro-detail-<?= $v['id'] ?>" name="color-pro-detail" <?= ($k == 0) ? "checked" : "" ?>>
                                </label>
                            <?php } else { ?>
                                <label for="color-pro-detail-<?= $v['id'] ?>" class="color-pro-detail text-decoration-none <?= ($k == 0) ? "active" : "" ?>" data-idproduct="<?= $rowDetail['id'] ?>" style="background-color: #<?= $v['color'] ?>">
                                    <input type="radio" value="<?= $v['id'] ?>" id="color-pro-detail-<?= $v['id'] ?>" name="color-pro-detail" <?= ($k == 0) ? "checked" : "" ?>>
                                </label>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </li>
            <?php } ?>
            <?php if (!empty($rowSize)) { ?>
                <li class="size-block-pro-detail w-clear">
                    <label class="attr-label-pro-detail d-block"><?= kichthuoc ?>:</label>
                    <div class="attr-content-pro-detail d-flex flex-wrap">
                        <?php foreach ($rowSize as $k => $v) { ?>
                            <label for="size-pro-detail-<?= $v['id'] ?>" class="size-pro-detail text-decoration-none <?= ($k == 0) ? "active" : "" ?>">
                                <input type="radio" value="<?= $v['id'] ?>" id="size-pro-detail-<?= $v['id'] ?>" name="size-pro-detail" <?= ($k == 0) ? "checked" : "" ?>>
                                <?= $v['name' . $lang] ?>
                            </label>
                        <?php } ?>
                    </div>
                </li>
            <?php } ?>
        </ul>

        <?php
        $productHotline = $optsetting['hotline'] ?? '';
        $productZaloLink = (!empty($qrzalo['idzalo']))
            ? $func->checkLinkZalo($qrzalo['idzalo'], $qrzalo['zalo'], $deviceType, $isIOS)
            : 'https://zalo.me/' . $func->parsePhone($qrzalo['zalo']);
        ?>
        <div class="product-contact-actions mt-3 mb-2">
            <a class="product-contact-button product-contact-zalo" target="_blank" rel="noopener" href="<?= $productZaloLink ?>" title="Yêu cầu tư vấn qua Zalo">
                <i class="fas fa-envelope"></i>
                <span>
                    <strong>Yêu cầu tư vấn</strong>
                    <small>Hãy để lại thông tin</small>
                </span>
            </a>
            <a class="product-contact-button product-contact-hotline" href="tel:<?= preg_replace('/[^0-9+]/', '', $productHotline) ?>" title="Gọi trực tiếp <?= $productHotline ?>">
                <i class="fas fa-phone-volume"></i>
                <span>
                    <strong>Gọi trực tiếp</strong>
                    <small>Hotline: <?= $productHotline ?></small>
                </span>
            </a>
        </div>
    </div>
</div>
<?php } ?>
<?php if (empty($quickview)) { ?>
    <div class="tags-pro-detail w-clear">
        <?php if (!empty($rowTags)) {
            foreach ($rowTags as $v) { ?>
                <a class="btn btn-sm btn-danger rounded" href="<?= $v[$sluglang] ?>" title="<?= $v['name' . $lang] ?>"><i class="fas fa-tags"></i><?= $v['name' . $lang] ?></a>
        <?php }
        } ?>
    </div>
    <div class="tabs-pro-detail">
        <ul class="nav nav-tabs" id="tabsProDetail" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="info-pro-detail-tab" data-bs-toggle="tab" href="#info-pro-detail" role="tab"><?= thongtinsanpham ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="thongsokythuat-pro-detail-tab" data-bs-toggle="tab" href="#thongsokythuat-pro-detail" role="tab"><?= thongsokythuat ?></a>
            </li>
        </ul>
        <div class="tab-content pt-4 pb-4" id="tabsProDetailContent">
            <div class="tab-pane fade show active" id="info-pro-detail" role="tabpanel">
                <div class="content-ck"><?= $func->autoLinkKeyword($func->getHtmlChars($rowDetail['content' . $lang])) ?></div>
            </div>

            <div class="tab-pane fade" id="thongsokythuat-pro-detail" role="tabpanel">
                <div class="content-ck"><?= $func->autoLinkKeyword($func->getHtmlChars($rowDetail['thongsokythuat' . $lang])) ?></div>
            </div>
        </div>
    </div>
<?php } ?>


<?php if (!empty($productCommentEnabled)) { ?>
    <?php include TEMPLATE . 'product/comment.php'; ?>
<?php } ?>
<?php if (empty($quickview)) { ?>
    <div class="title-main"><span><?= sanphamcungloai ?></span></div>
    <?php if (!empty($product)) { ?>
        <div class="row-product row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4">
            <?= $func->getProductItems($product, $sluglang) ?>
        </div>
    <?php } else { ?>
        <div class="alert alert-warning w-100" role="alert">
            <strong><?= khongtimthayketqua ?></strong>
        </div>
    <?php } ?>
    <div class="pagination-home w-100"><?= (!empty($paging)) ? $paging : '' ?></div>
<?php } ?>
<?php if (empty($quickview)) { ?>
    <?php if (!empty($_SESSION['pro_seen'])) { ?>
        <div class="title-main mt-4"><span>Bạn vừa xem </span></div>
        <div class="owl-page owl-carousel owl-theme" data-items="screen:0|items:2|margin:10,screen:425|items:2|margin:10,screen:575|items:2|margin:10,screen:767|items:3|margin:10,screen:991|items:4|margin:20,screen:1199|items:4|margin:20" data-rewind="1" data-autoplay="1" data-loop="0" data-lazyload="0" data-mousedrag="1" data-touchdrag="1" data-smartspeed="300" data-autoplayspeed="500" data-autoplaytimeout="3500" data-dots="0" data-nav="0" data-navcontainer="">
            <?php
            $productSeenItems = array();
            foreach ($_SESSION['pro_seen'] as $productSeenId) {
                $detailProduct = $func->getInfoDetail("id, name$lang, slugvi, slugen, photo, discount, sale_price, regular_price, rating, rating_average, rating_count", 'product', $productSeenId);
                if (!empty($detailProduct['id'])) $productSeenItems[] = $detailProduct;
            }
            ?>
            <?= $func->getProductItems($productSeenItems, $sluglang, true) ?>
        </div>
    <?php } ?>
<?php } ?>
