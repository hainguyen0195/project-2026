<?php
if (!defined('SOURCES')) die("Error");

/* Lấy bài viết tĩnh */
$aboutSelect = "id, type, name$lang, desc$lang, content$lang, photo, date_created, date_updated, options";
$static = $d->rawQueryOne("select $aboutSelect from #_static where type = ? limit 0,1", array($type));

/* Các khối nội dung trang giới thiệu dùng chung bảng static để tránh phụ thuộc bảng tùy biến. */


$tamnhin = $d->rawQueryOne("select $aboutSelect from #_static where type = ? limit 0,1", array('tam-nhin'));
$sumenh = $d->rawQueryOne("select $aboutSelect from #_static where type = ? limit 0,1", array('su-menh'));
$giatricotloi = $d->rawQueryOne("select $aboutSelect from #_static where type = ? limit 0,1", array('gia-tri-cot-loi'));
$quytrinhlamviec = $d->rawQueryOne("select $aboutSelect from #_static where type = ? limit 0,1", array('quy-trinh-lam-viec'));

$lichsuhinhthanh = $d->rawQuery("select name$lang, desc$lang, photo, giaidoan1$lang, giaidoan2$lang from #_news where type = ? and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac', status) = 0 OR status IS NULL) order by numb,id asc", array('lich-su-hinh-thanh'));

$bannerlichsuhinhthanh = $d->rawQueryOne("select photo from #_photo where type = ? and act = ? limit 0,1", array('banner-lich-su-hinh-thanh', 'photo_static'));
$thanhtuu = $d->rawQuery("select name$lang, desc$lang, photo,number from #_news where type = ? and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac', status) = 0 OR status IS NULL) order by numb,id asc", array('thanh-tuu'));

$duandatrienkhai = $d->rawQuery("select name$lang, slugvi, slugen, desc$lang, date_created, id, photo, address$lang from #_news where type = ? and find_in_set('noibat',status) and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL) order by numb,id desc", array('du-an'));


/* SEO */
if (!empty($static)) {
	$seoDB = $seo->getOnDB(0, 'static', 'update', $static['type']);
	$seo->set('h1', $static['name' . $lang]);
	if (!empty($seoDB['title' . $seolang])) $seo->set('title', $seoDB['title' . $seolang]);
	else $seo->set('title', $static['name' . $lang]);
	if (!empty($seoDB['keywords' . $seolang])) $seo->set('keywords', $seoDB['keywords' . $seolang]);
	if (!empty($seoDB['description' . $seolang])) $seo->set('description', $seoDB['description' . $seolang]);
	$seo->set('url', $func->getPageURL());
	$imgJson = (!empty($static['options'])) ? json_decode($static['options'], true) : null;
	if (empty($imgJson) || ($imgJson['p'] != $static['photo'])) {
		$imgJson = $func->getImgSize($static['photo'], UPLOAD_NEWS_L . $static['photo']);
		$seo->updateSeoDB(json_encode($imgJson), 'static', $static['id']);
	}
	if (!empty($imgJson)) {
		$seo->set('photo', $configBase . THUMBS . '/' . $imgJson['w'] . 'x' . $imgJson['h'] . 'x2/' . UPLOAD_NEWS_L . $static['photo']);
		$seo->set('photo:width', $imgJson['w']);
		$seo->set('photo:height', $imgJson['h']);
		$seo->set('photo:type', $imgJson['m']);
	}
}

/* breadCrumbs */
if (!empty($titleMain)) $breadcr->set($com, $titleMain);
$breadcrumbs = $breadcr->get();
