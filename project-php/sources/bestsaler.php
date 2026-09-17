<?php
if (!defined('SOURCES')) die("Error");

$perPage = 9;
$curPage = max(1, (int)$getPage);
$startpoint = ($curPage - 1) * $perPage;
$select = "id, name$lang, desc$lang, slugvi, slugen, photo, regular_price, sale_price, discount, rating, rating_average, rating_count";
$where = "type = ? and find_in_set('bestsaler',status) and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL)";
$params = array($type);

$count = $d->rawQueryOne("select count(*) as num from #_product where $where", $params);
$total = !empty($count['num']) ? (int)$count['num'] : 0;
$product = $d->rawQuery(
    "select $select from #_product where $where order by numb,id desc limit $startpoint,$perPage",
    $params
);

$url = $func->getCurrentPageURL();
$paging = $func->pagination($total, $perPage, $curPage, $url);

$seopage = $d->rawQueryOne("select * from #_seopage where type = ? limit 0,1", array('best-saler'));
$pageDescription = !empty($seopage['description' . $seolang])
    ? trim(strip_tags(htmlspecialchars_decode($seopage['description' . $seolang])))
    : 'Sản phẩm làm đẹp hiệu quả và có nguồn gốc từ thiên nhiên';

$seo->set('h1', $titleMain);
$seo->set('title', !empty($seopage['title' . $seolang]) ? $seopage['title' . $seolang] : $titleMain);
$seo->set('description', $pageDescription);
if (!empty($seopage['keywords' . $seolang])) $seo->set('keywords', $seopage['keywords' . $seolang]);
$seo->set('url', $func->getPageURL());

$breadcr->set($com, $titleMain);
$breadcrumbs = $breadcr->get();
