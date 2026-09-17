<?php
$productNameSafe = htmlspecialchars($rowDetail['name' . $lang], ENT_QUOTES, 'UTF-8');
$productUrl = $func->getPageURL();
$galleryImages = array(array('photo' => $rowDetail['photo']));
foreach ($rowDetailPhoto as $galleryPhoto) if (!empty($galleryPhoto['photo'])) $galleryImages[] = $galleryPhoto;
$flashEnd = !empty($productFlashSale['end_at']) ? strtotime($productFlashSale['end_at']) * 1000 : 0;
$aiText = function ($key) use ($aiProduct) {
    $value = isset($aiProduct[$key]) ? (string)$aiProduct[$key] : '';
    return htmlspecialchars(htmlspecialchars_decode($value, ENT_QUOTES), ENT_QUOTES, 'UTF-8');
};
$aiBrand = !empty($aiProduct['brand']) ? (string)$aiProduct['brand'] : (!empty($productBrand['name' . $lang]) ? (string)$productBrand['name' . $lang] : '');
$aiRows = array(
    'purchase_note' => 'Mua sản phẩm',
    'shipping' => 'Giao hàng',
    'return_policy' => 'Đổi trả',
    'warranty' => 'Bảo hành',
    'origin' => 'Xuất xứ'
);
$aiAvailabilityLabels = array('InStock' => 'Còn hàng', 'OutOfStock' => 'Hết hàng', 'PreOrder' => 'Đặt trước', 'BackOrder' => 'Đặt hàng bổ sung');
$aiConditionLabels = array('NewCondition' => 'Mới', 'UsedCondition' => 'Đã qua sử dụng', 'RefurbishedCondition' => 'Tân trang');
$aiProductVisible = !isset($aiProduct['enabled']) || (string)$aiProduct['enabled'] !== '0';
$aiHasContent = !empty($config['website']['ai_product']) && $aiProductVisible && (!empty($aiProduct['brand']) || !empty($aiProduct['gtin']) || !empty($aiProduct['mpn']) || !empty($aiProduct['availability']) || !empty($aiProduct['condition']));
foreach ($aiRows as $aiKey => $aiLabel) if ($aiProductVisible && !empty($aiProduct[$aiKey])) $aiHasContent = true;
?>
<div class="grid-pro-detail product-detail-new d-flex flex-wrap justify-content-between align-items-start">
    <div class="left-pro-detail product-gallery-new">
        <div class="product-gallery-new__thumbs">
            <?php foreach (array_slice($galleryImages, 0, 5) as $index => $galleryImage) { ?>
                <a class="thumb-pro-detail product-gallery-thumb<?= $index === 0 ? ' is-active' : '' ?>" data-zoom-id="Zoom-1" data-gallery-index="<?= $index ?>" href="<?= THUMBS ?>/1600x1600x1/<?= UPLOAD_PRODUCT_L . $galleryImage['photo'] ?>" data-image="<?= THUMBS ?>/1040x1040x1/<?= UPLOAD_PRODUCT_L . $galleryImage['photo'] ?>" title="<?= $productNameSafe ?>">
                    <img src="<?= THUMBS ?>/130x130x1/<?= UPLOAD_PRODUCT_L . $galleryImage['photo'] ?>" alt="<?= $productNameSafe ?>">
                </a>
            <?php } ?>
        </div>
        <div class="product-gallery-new__main">
            <a id="Zoom-1" class="MagicZoom" data-options="zoomMode: zoom; zoomPosition: inner; zoomOn: hover; hint: always; textHoverZoomHint: Di chuột để phóng to; expand: off;" href="<?= THUMBS ?>/1600x1600x1/<?= UPLOAD_PRODUCT_L . $rowDetail['photo'] ?>" title="<?= $productNameSafe ?>">
                <img src="<?= THUMBS ?>/1040x1040x1/<?= UPLOAD_PRODUCT_L . $rowDetail['photo'] ?>" alt="<?= $productNameSafe ?>">
            </a>
            <button type="button" class="product-gallery-expand" aria-label="Xem thư viện ảnh toàn màn hình"><i class="far fa-search-plus"></i></button>
        </div>
        <div class="product-gallery-lightbox d-none"><?php foreach ($galleryImages as $index => $galleryImage) { ?><a data-fancybox="product-detail-gallery" data-gallery-index="<?= $index ?>" href="<?= THUMBS ?>/1800x1800x1/<?= UPLOAD_PRODUCT_L . $galleryImage['photo'] ?>" data-caption="<?= $productNameSafe ?>"></a><?php } ?></div>
    </div>
    <div class="right-pro-detail product-summary-new">
        <h1 class="title-pro-detail"><?= $rowDetail['name' . $lang] ?></h1>
        <div class="product-summary-new__meta">
            <span class="product-rating-score"><i class="fas fa-star"></i> <?= number_format($productRatingAverage, 1) ?></span>
            <span class="product-rating-stars"><?php for ($star = 1; $star <= 5; $star++) { ?><i class="<?= $star <= round($productRatingAverage) ? 'fas' : 'far' ?> fa-star"></i><?php } ?></span>
            <?php if ($productRatingHasReviews) { ?><span>(<?= $productRatingTotal ?> đánh giá)</span><?php } else { ?><span>Chưa có đánh giá</span><?php } ?>
            <span class="meta-separator"></span><span>Đã bán <?= $productSold ?></span>
        </div>
        <div class="product-price-panel<?= $flashEnd ? ' has-flash-sale' : '' ?>">
            <div class="product-price-panel__price">
                <strong><?= $rowDetail['sale_price'] > 0 ? $func->formatMoney($rowDetail['sale_price']) : (($rowDetail['regular_price'] > 0) ? $func->formatMoney($rowDetail['regular_price']) : lienhe) ?></strong>
                <?php if ($rowDetail['sale_price'] > 0) { ?><div><del><?= $func->formatMoney($rowDetail['regular_price']) ?></del><?php if ($rowDetail['discount'] > 0) { ?><span>Tiết kiệm tới <?= round($rowDetail['discount']) ?>%</span><?php } ?></div><?php } ?>
            </div>
            <?php if ($flashEnd) { ?><div class="product-price-panel__flash" data-flash-sale-end="<?= $flashEnd ?>" data-flash-sale-item><b>SIÊU SALE CHỚP NHOÁNG</b><span>Kết thúc sau</span><strong class="product-flash-sale__time">00 : 00 : 00 : 00</strong></div><?php } ?>
        </div>
        <ul class="attr-pro-detail product-options-new">
            <?php if (!empty($rowSize)) { ?><li class="size-block-pro-detail"><label class="attr-label-pro-detail">Phân loại</label><div class="attr-content-pro-detail d-flex flex-wrap"><?php foreach ($rowSize as $k => $v) { ?><label for="size-pro-detail-<?= $v['id'] ?>" class="size-pro-detail<?= $k === 0 ? ' active' : '' ?>"><input type="radio" value="<?= $v['id'] ?>" id="size-pro-detail-<?= $v['id'] ?>" name="size-pro-detail" <?= $k === 0 ? 'checked' : '' ?>><?= $v['name' . $lang] ?></label><?php } ?></div></li><?php } ?>
            <li class="product-quantity-row"><label class="attr-label-pro-detail">Số lượng</label><div class="quantity-pro-detail"><span class="quantity-minus-pro-detail">−</span><input type="number" class="qty-pro" min="1" value="1"><span class="quantity-plus-pro-detail">+</span></div></li>
        </ul>
        <div class="cart-pro-detail product-buy-actions d-flex flex-wrap"><a class="addcart addnow d-flex align-items-center justify-content-center" data-id="<?= $rowDetail['id'] ?>" data-action="addnow">Thêm vào giỏ hàng <i class="far fa-arrow-right"></i></a><a class="addcart buynow d-flex align-items-center justify-content-center" data-id="<?= $rowDetail['id'] ?>" data-action="buynow">Mua ngay</a></div>
        <?php if ($aiHasContent) { ?>
            <section class="product-ai-facts" aria-label="Thông tin mua sản phẩm">
                <h2 class="product-ai-facts__title">Thông tin sản phẩm và mua hàng</h2>
                <?php if ($aiBrand) { ?><div class="product-ai-facts__row"><strong>Thương hiệu</strong><span><?= $aiText('brand') ?: htmlspecialchars(htmlspecialchars_decode($aiBrand, ENT_QUOTES), ENT_QUOTES, 'UTF-8') ?></span></div><?php } ?>
                <?php if (!empty($aiProduct['gtin'])) { ?><div class="product-ai-facts__row"><strong>GTIN/EAN/UPC</strong><span><?= $aiText('gtin') ?></span></div><?php } ?>
                <?php if (!empty($aiProduct['mpn'])) { ?><div class="product-ai-facts__row"><strong>MPN</strong><span><?= $aiText('mpn') ?></span></div><?php } ?>
                <?php if (!empty($aiAvailabilityLabels[$aiProduct['availability'] ?? ''])) { ?><div class="product-ai-facts__row"><strong>Tình trạng</strong><span><?= $aiAvailabilityLabels[$aiProduct['availability']] ?></span></div><?php } ?>
                <?php if (!empty($aiConditionLabels[$aiProduct['condition'] ?? ''])) { ?><div class="product-ai-facts__row"><strong>Sản phẩm</strong><span><?= $aiConditionLabels[$aiProduct['condition']] ?></span></div><?php } ?>
                <?php foreach ($aiRows as $aiKey => $aiLabel) { if (!empty($aiProduct[$aiKey])) { ?>
                    <div class="product-ai-facts__row"><strong><?= $aiLabel ?></strong><span><?= $aiKey === 'purchase_note' ? nl2br($aiText($aiKey)) : $aiText($aiKey) ?></span></div>
                <?php }} ?>
            </section>
        <?php } ?>
        <?php if (empty($quickview)) { ?><div class="product-share-new"><span>Chia sẻ:</span><a target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($productUrl) ?>" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a><a target="_blank" rel="noopener" href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($productUrl) ?>" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a><a target="_blank" rel="noopener" href="https://twitter.com/intent/tweet?url=<?= urlencode($productUrl) ?>&text=<?= urlencode($rowDetail['name' . $lang]) ?>" aria-label="Twitter"><i class="fab fa-twitter"></i></a></div><?php } ?>
    </div>
</div>
<script>
var productGalleryIndex = 0;
document.querySelectorAll('.product-gallery-thumb').forEach(function (thumb) {
    thumb.addEventListener('click', function () {
        productGalleryIndex = Number(thumb.getAttribute('data-gallery-index')) || 0;
        document.querySelectorAll('.product-gallery-thumb').forEach(function (item) { item.classList.remove('is-active'); });
        thumb.classList.add('is-active');
    });
});
function openProductGallery() {
    var links = Array.prototype.slice.call(document.querySelectorAll('[data-fancybox="product-detail-gallery"]'));
    if (!links.length || typeof Fancybox === 'undefined') return;
    Fancybox.show(links.map(function (link) { return { src: link.href, type: 'image', caption: link.getAttribute('data-caption') || '' }; }), { startIndex: productGalleryIndex });
}
var expandButton = document.querySelector('.product-gallery-expand');
if (expandButton) expandButton.addEventListener('click', openProductGallery);
var zoomMain = document.getElementById('Zoom-1');
if (zoomMain) zoomMain.addEventListener('click', function (event) { event.preventDefault(); event.stopPropagation(); openProductGallery(); }, true);
</script>
