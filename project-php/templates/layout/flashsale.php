<?php
$flashSaleProducts = array();
if ($d->tableExists('product_flash_sale')) {
    $flashSaleProducts = $d->rawQuery(
        "select p.id, p.name$lang, p.desc$lang, p.slugvi, p.slugen, p.photo, p.rating, p.rating_average, p.rating_count, fs.original_price as regular_price, fs.flash_price as sale_price, fs.end_at
        from #_product_flash_sale fs inner join #_product p on p.id = fs.product_id
        where fs.start_at <= NOW() and fs.end_at > NOW() and p.type = ?
            and find_in_set('hienthi',p.status)
            and (find_in_set('thungrac',p.status) = 0 or p.status is null)
        order by fs.end_at asc, p.numb asc, p.id desc",
        array('san-pham')
    );
}
?>

<?php if (!empty($flashSaleProducts)) { ?>
    <section class="home-flash-sale sec-htab">
        <div class="container-custom">
            <h2 class="home-flash-sale__title">Combo Sale</h2>
            <div class="home-flash-sale__slider sprd-list" data-flash-sale-slider>
                <?php foreach ($flashSaleProducts as $flashProduct) {
                    echo $func->renderProductCard($flashProduct, array('wrapper_class' => 'home-flash-sale__slide col reveal-it'));
                } ?>
            </div>
            <div class="home-flash-sale__more"><a href="san-pham"><?= xemtatca ?> <i class="far fa-arrow-right"></i></a></div>
        </div>
    </section>
<?php } ?>
