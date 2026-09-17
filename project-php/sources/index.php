<?php
if (!defined('SOURCES')) die("Error");
$popup = $cache->get("select name$lang, photo, link from #_photo where type = ? and act = ? and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL) limit 0,1", array('popup', 'photo_static'), 'fetch', 7200);
$slider = $cache->get("select name$lang, text1$lang, text2$lang, desc$lang, photo, link, link2, link_video, options from #_photo where type = ? and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL) order by numb,id desc", array('slide'), 'result', 7200);
$slideBlogs = array();
foreach ($slider as $slideItem) {
    $slideOptions = !empty($slideItem['options']) ? json_decode($slideItem['options'], true) : array();
    $slideNewsIds = !empty($slideOptions['news_ids']) && is_array($slideOptions['news_ids'])
        ? array_unique(array_filter(array_map('intval', $slideOptions['news_ids'])))
        : array();

    foreach ($slideNewsIds as $slideNewsId) {
        if (!isset($slideBlogs[$slideNewsId])) {
            $slideBlogs[$slideNewsId] = $d->rawQueryOne(
                "select id, name$lang, desc$lang, slugvi, slugen, photo from #_news where id = ? and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL) limit 0,1",
                array($slideNewsId)
            );
        }
    }
}


$titleProductHome = $d->rawQueryOne("select name$lang, desc$lang from #_static where type = ? limit 0,1", array('title-product-home'));
$productHot = $d->rawQuery("select id, name$lang, slugvi, slugen, photo, regular_price, sale_price, discount, rating, rating_average, rating_count from #_product where type = ? and find_in_set('noibat',status) and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL) order by numb,id desc limit 8", array('san-pham'));
$productBestsaler = $d->rawQuery("select id, name$lang, desc$lang, slugvi, slugen, photo, regular_price, sale_price, discount, rating, rating_average, rating_count from #_product where type = ? and find_in_set('bestsaler',status) and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL) order by numb,id desc limit 6", array('san-pham'));
$productNew = $d->rawQuery("select id, name$lang, desc$lang, slugvi, slugen, photo, regular_price, sale_price, discount, rating, rating_average, rating_count from #_product where type = ? and find_in_set('new',status) and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL) order by numb,id desc limit 6", array('san-pham'));
$newProductSection = $d->rawQueryOne("select name$lang, text1$lang, text2$lang, text3$lang, photo, options, tieuchi, status from #_static where type = ? limit 0,1", array('san-pham-moi'));
$newProductSectionOptions = !empty($newProductSection['options']) ? json_decode($newProductSection['options'], true) : array();
$newProductSectionProductId = !empty($newProductSectionOptions['product_id']) ? (int)$newProductSectionOptions['product_id'] : 0;
$newProductSectionProduct = array();
if ($newProductSectionProductId > 0) {
    $newProductSectionProduct = $d->rawQueryOne(
        "select id, name$lang, desc$lang, slugvi, slugen, photo, regular_price, sale_price, discount, rating, rating_average, rating_count from #_product where id = ? and type = ? and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL) limit 0,1",
        array($newProductSectionProductId, 'san-pham')
    );
}
$why = $d->rawQuery("select name$lang, desc$lang, photo from #_news where type = ? and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac', status) = 0 OR status IS NULL) order by numb,id desc", array('vi-sao-chon-chung-toi'));
$logoWhy = $d->rawQueryOne("select name$lang, photo from #_photo where type = ? and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac', status) = 0 OR status IS NULL) limit 0,1", array('logo-why'));

$duanHot = $d->rawQuery("select name$lang, slugvi, slugen, desc$lang, date_created, id, photo, address$lang from #_news where type = ? and find_in_set('noibat',status) and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL) order by numb,id desc", array('du-an'));



/* SEO */
$seopage = $d->rawQueryOne("select * from #_seopage where type = ? limit 0,1", array('trang-chu'));

$seo->set('h1', $seopage['title' . $seolang]);
if (!empty($seopage['title' . $seolang])) $seo->set('title', $seopage['title' . $seolang]);
else $seo->set('title', $titleMain);
if (!empty($seopage['keywords' . $seolang])) $seo->set('keywords', $seopage['keywords' . $seolang]);
if (!empty($seopage['description' . $seolang])) $seo->set('description', $seopage['description' . $seolang]);
$seo->set('url', $func->getPageURL());
$imgJson = (!empty($seopage['options'])) ? json_decode($seopage['options'], true) : null;
if (!empty($seopage['photo'])) {
    if (empty($imgJson) || ($imgJson['p'] != $seopage['photo'])) {
        $imgJson = $func->getImgSize($seopage['photo'], UPLOAD_SEOPAGE_L . $seopage['photo']);
        $seo->updateSeoDB(json_encode($imgJson), 'seopage', $seopage['id']);
    }
    if (!empty($imgJson)) {
        $seo->set('photo', $configBase . THUMBS . '/' . $imgJson['w'] . 'x' . $imgJson['h'] . 'x2/' . UPLOAD_SEOPAGE_L . $seopage['photo']);
        $seo->set('photo:width', $imgJson['w']);
        $seo->set('photo:height', $imgJson['h']);
        $seo->set('photo:type', $imgJson['m']);
    }
}
