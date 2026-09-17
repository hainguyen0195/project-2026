<?php
if (!defined('SOURCES')) die("Error");

$perPage = 9;
$curPage = max(1, (int)$getPage);
$startpoint = ($curPage - 1) * $perPage;
$product = array();
$total = 0;
$flashSaleEnd = 0;

if ($d->tableExists('product_flash_sale')) {
    $where = "fs.start_at <= NOW() and fs.end_at > NOW() and p.type = ? and find_in_set('hienthi',p.status) and (find_in_set('thungrac',p.status) = 0 or p.status is null)";
    $params = array($type);
    $count = $d->rawQueryOne("select count(*) as num, min(fs.end_at) as nearest_end from #_product_flash_sale fs inner join #_product p on p.id = fs.product_id where $where", $params);
    $total = !empty($count['num']) ? (int)$count['num'] : 0;
    $flashSaleEnd = !empty($count['nearest_end']) ? strtotime($count['nearest_end']) * 1000 : 0;
    $product = $d->rawQuery(
        "select p.id, p.name$lang, p.desc$lang, p.slugvi, p.slugen, p.photo, p.rating, p.rating_average, p.rating_count, fs.original_price as regular_price, fs.flash_price as sale_price,
            case when fs.original_price > 0 then round(100 - (fs.flash_price * 100 / fs.original_price)) else 0 end as discount, fs.end_at
        from #_product_flash_sale fs inner join #_product p on p.id = fs.product_id
        where $where order by fs.end_at asc,p.numb asc,p.id desc limit $startpoint,$perPage",
        $params
    );
}

$url = $func->getCurrentPageURL();
$paging = $func->pagination($total, $perPage, $curPage, $url);
$seo->set('h1', $titleMain);
$seo->set('title', $titleMain);
$seo->set('description', 'Ưu đãi Flash Sale dành cho bạn');
$seo->set('url', $func->getPageURL());
$breadcr->set($com, $titleMain);
$breadcrumbs = $breadcr->get();
