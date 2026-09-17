<?php if (!empty($productOverview)) { ?>
    <div class="product-all-categories">
        <?php $hasOverviewProduct = false; ?>
        <?php foreach ($productOverviewGroups as $group) {
            if (empty($group['products'])) continue;
            $hasOverviewProduct = true;
            $groupName = $group['name' . $lang];
            $groupDesc = trim(strip_tags(htmlspecialchars_decode($group['desc' . $lang] ?? '')));
        ?>
            <section class="sec-htab best-seller-page product-list-section">
                <header class="best-seller-page__header product-list-section__header">
                    <div class="best-seller-page__title-row">
                        <h2><a href="<?= $group[$sluglang] ?>" title="<?= htmlspecialchars($groupName, ENT_QUOTES, 'UTF-8') ?>"><?= mb_strtoupper($groupName, 'UTF-8') ?></a></h2>
                        <span class="best-seller-page__count" aria-label="<?= (int)$group['total'] ?> sản phẩm"><?= (int)$group['total'] ?></span>
                    </div>
                    <?php if ($groupDesc !== '') { ?><p class="best-seller-page__description"><?= htmlspecialchars($groupDesc, ENT_QUOTES, 'UTF-8') ?></p><?php } ?>
                </header>

                <div class="sprd-list best-seller-page__grid">
                    <?php foreach ($group['products'] as $overviewProduct) echo $func->renderProductCard($overviewProduct, array('heading_tag' => 'h3')); ?>
                </div>

                <?php if ((int)$group['total'] > 8) { ?>
                    <div class="product-list-section__more"><a href="<?= $group[$sluglang] ?>">Xem tất cả <i class="far fa-arrow-right"></i></a></div>
                <?php } ?>
            </section>
        <?php } ?>

        <?php if (!$hasOverviewProduct) { ?><div class="alert alert-warning w-100" role="alert"><strong><?= khongtimthayketqua ?></strong></div><?php } ?>
    </div>

<?php } else if (!empty($idl) || !empty($idc) || !empty($idi) || !empty($ids)) { ?>
    <?php
    $currentCategory = !empty($productSub) ? $productSub : (!empty($productItem) ? $productItem : (!empty($productCat) ? $productCat : $productList));
    $categoryName = $currentCategory['name' . $lang] ?? $titleMain;
    $categoryDesc = !empty($idl) && !empty($productList['desc' . $lang]) ? trim(strip_tags(htmlspecialchars_decode($productList['desc' . $lang]))) : '';
    ?>
    <section class="sec-htab best-seller-page product-list-section product-list-category-page">
        <header class="best-seller-page__header product-list-section__header">
            <div class="best-seller-page__title-row">
                <h1><?= mb_strtoupper($categoryName, 'UTF-8') ?></h1>
                <span class="best-seller-page__count" aria-label="<?= (int)$total ?> sản phẩm"><?= (int)$total ?></span>
            </div>
            <?php if ($categoryDesc !== '') { ?><p class="best-seller-page__description"><?= htmlspecialchars($categoryDesc, ENT_QUOTES, 'UTF-8') ?></p><?php } ?>
        </header>

        <?php if (!empty($product)) { ?>
            <div class="sprd-list best-seller-page__grid">
                <?php foreach ($product as $categoryProduct) echo $func->renderProductCard($categoryProduct, array('heading_tag' => 'h3')); ?>
            </div>
        <?php } else { ?>
            <div class="alert alert-warning w-100" role="alert"><strong><?= khongtimthayketqua ?></strong></div>
        <?php } ?>

        <?php if (!empty($paging)) { ?><nav class="best-seller-page__pagination pagination-home" aria-label="Phân trang sản phẩm"><?= $paging ?></nav><?php } ?>
        <?php if (!empty($idl) && !empty($productList['content' . $lang])) { ?><div class="desc-cat-level1 mt-5"><?= htmlspecialchars_decode($productList['content' . $lang]) ?></div><?php } ?>
    </section>

<?php } else { ?>
    <?php if ($com == 'tim-kiem') { ?><div class="div_kq_search mb-4"><?= $titleMain ?> (<?= $total ?>): <span>"<?= $tukhoa_show ?>"</span></div><?php } ?>
    <?php if (!empty($product)) { ?>
        <div class="sprd-list best-seller-page__grid sec-htab">
            <?php foreach ($product as $itemProduct) echo $func->renderProductCard($itemProduct); ?>
        </div>
    <?php } else { ?>
        <div class="alert alert-warning w-100" role="alert"><strong><?= khongtimthayketqua ?></strong></div>
    <?php } ?>
    <div class="pagination-home w-100"><?= (!empty($paging)) ? $paging : '' ?></div>
<?php } ?>
