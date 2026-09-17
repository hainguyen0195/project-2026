<section class="sec-htab best-seller-page">
    <header class="best-seller-page__header">
        <div class="best-seller-page__title-row">
            <h1>BEST SELLERS</h1>
            <span class="best-seller-page__count" aria-label="<?= $total ?> sản phẩm"><?= $total ?></span>
        </div>
        <?php if ($pageDescription !== '') { ?><p class="best-seller-page__description"><?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?></p><?php } ?>
    </header>

    <?php if (!empty($product)) { ?>
        <div class="sprd-list best-seller-page__grid">
            <?php foreach ($product as $v) echo $func->renderProductCard($v, array('heading_tag' => 'h2')); ?>
        </div>
    <?php } else { ?>
        <div class="alert alert-warning w-100" role="alert"><strong><?= khongtimthayketqua ?></strong></div>
    <?php } ?>

    <?php if (!empty($paging)) { ?><nav class="best-seller-page__pagination pagination-home" aria-label="Phân trang sản phẩm"><?= $paging ?></nav><?php } ?>
</section>
