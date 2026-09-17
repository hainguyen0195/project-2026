<section class="sec-htab flash-sale-page">
    <header class="flash-sale-page__hero" data-flash-sale-end="<?= $flashSaleEnd ?>" data-flash-sale-item>
        <h1>Ưu Đãi Dành Cho Bạn</h1>
        <?php if ($flashSaleEnd > 0) { ?>
            <div class="flash-sale-page__countdown" aria-label="Thời gian Flash Sale còn lại">
                <?php foreach (array('days' => 'Ngày', 'hours' => 'Giờ', 'minutes' => 'Phút', 'seconds' => 'Giây') as $unit => $label) { ?>
                    <div class="flash-sale-page__unit"><strong data-flash-unit="<?= $unit ?>">00</strong><span><?= $label ?></span></div>
                <?php } ?>
            </div>
        <?php } ?>
    </header>

    <?php if (!empty($product)) { ?>
        <div class="sprd-list flash-sale-page__grid">
            <?php foreach ($product as $v) echo $func->renderProductCard($v, array('heading_tag' => 'h2')); ?>
        </div>
    <?php } else { ?>
        <div class="alert alert-warning w-100" role="alert"><strong>Hiện chưa có chương trình Flash Sale đang diễn ra.</strong></div>
    <?php } ?>
    <?php if (!empty($paging)) { ?><nav class="flash-sale-page__pagination pagination-home" aria-label="Phân trang Flash Sale"><?= $paging ?></nav><?php } ?>
</section>
