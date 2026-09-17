<?php
$newProduct = !empty($newProductSectionProduct) ? $newProductSectionProduct : (!empty($productNew) ? $productNew[0] : array());
$newProductSectionEnabled = empty($newProductSection) || strpos((string)($newProductSection['status'] ?? ''), 'hienthi') !== false;

if ($newProductSectionEnabled && !empty($newProduct)) {
    $newProductName = trim((string)($newProduct['name' . $lang] ?: ($newProduct['namevi'] ?? sanpham)));
    $newProductDesc = trim(strip_tags(htmlspecialchars_decode($newProduct['desc' . $lang] ?? '')));
    $newProductUrl = $newProduct[$sluglang];

    $sectionTitle = trim($func->decodeHtmlChars($newProductSection['name' . $lang] ?? '')) ?: 'SẢN PHẨM MỚI';
    $brandParts = array(
        trim($func->decodeHtmlChars($newProductSection['text1' . $lang] ?? '')) ?: 'comi viet nam',
    );
    $brandTitle = implode(' ', $brandParts);

    $sectionLabels = array();
    if (!empty($newProductSection['tieuchi'])) {
        $decodedLabels = json_decode(htmlspecialchars_decode($newProductSection['tieuchi'], ENT_QUOTES), true);
        if (is_array($decodedLabels)) {
            foreach ($decodedLabels as $labelItem) {
                $label = is_array($labelItem) ? trim((string)($labelItem['label'] ?? '')) : trim((string)$labelItem);
                if ($label !== '') $sectionLabels[] = $label;
            }
        }
    }
    if (empty($sectionLabels)) $sectionLabels = array('Chính hãng', 'Mới nhất', 'Chất lượng');
    $sectionLabels = array_slice($sectionLabels, 0, 3);

    $sectionPhoto = !empty($newProductSection['photo'])
        ? THUMBS . '/770x770x1/' . UPLOAD_NEWS_L . $newProductSection['photo']
        : THUMBS . '/770x770x1/' . UPLOAD_PRODUCT_L . $newProduct['photo'];
?>
    <section class="sec-abp ss-pd custom" aria-labelledby="comi-new-product-title">
        <div class="container">
            <div class="abp">
                <div class="abp-wrap">
                    <div class="abp-head">
                        <h2 class="t-sub" id="comi-new-product-title"><?= htmlspecialchars($sectionTitle, ENT_QUOTES, 'UTF-8') ?></h2>
                        <div class="abp-title" aria-label="<?= htmlspecialchars($brandTitle, ENT_QUOTES, 'UTF-8') ?>">
                            <span class="t-text" id="heroTextAnim"><?= htmlspecialchars($brandTitle, ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    </div>
                    <div class="abp-block">
                        <div class="abp-row row">
                            <div class="col col-6">
                                <div class="abp-pos">
                                    <div class="abp-group">
                                        <div class="abp-image">
                                            <a class="inner" href="<?= $newProductUrl ?>" title="<?= htmlspecialchars($newProductName, ENT_QUOTES, 'UTF-8') ?>">
                                                <img class="lazy" width="770" height="770" onerror="this.src='<?= THUMBS ?>/770x770x1/assets/images/noimage.png';" data-src="<?= $sectionPhoto ?>" alt="<?= htmlspecialchars($newProductName, ENT_QUOTES, 'UTF-8') ?>">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="abp-abs">
                                        <div class="b-head">
                                            <div class="b-list" aria-label="Đặc điểm sản phẩm">
                                                <?php foreach ($sectionLabels as $sectionLabel) { ?>
                                                    <div class="b-item"><span class="t-text"><?= htmlspecialchars($sectionLabel, ENT_QUOTES, 'UTF-8') ?></span></div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <article class="abp-product-card">
                                            <?= $func->getFlashSaleBadge((int)$newProduct['id']) ?>
                                            <div class="abp-product-card__head">
                                                <h3 class="abp-product-card__name">
                                                    <a href="<?= $newProductUrl ?>"><?= htmlspecialchars($newProductName, ENT_QUOTES, 'UTF-8') ?></a>
                                                </h3>
                                                <a class="abp-product-card__arrow" href="<?= $newProductUrl ?>" aria-label="Xem <?= htmlspecialchars($newProductName, ENT_QUOTES, 'UTF-8') ?>">
                                                    <i class="far fa-arrow-right"></i>
                                                </a>
                                            </div>
                                            <button type="button" class="abp-product-card__price addcart" data-id="<?= (int)$newProduct['id'] ?>" data-action="addnow" aria-label="<?= dathang ?> - <?= htmlspecialchars($newProductName, ENT_QUOTES, 'UTF-8') ?>">
                                                <strong><?= $newProduct['sale_price'] > 0 ? $func->formatMoney($newProduct['sale_price']) : (($newProduct['regular_price'] > 0) ? $func->formatMoney($newProduct['regular_price']) : lienhe) ?></strong>
                                                <?php if ($newProduct['sale_price'] > 0) { ?><del><?= $func->formatMoney($newProduct['regular_price']) ?></del><?php } ?>
                                            </button>
                                        </article>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } ?>
