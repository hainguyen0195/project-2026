<?php
if (!defined('SOURCES')) die("Error");

/* static */
$favicon = $cache->get("select photo from #_photo where type = ? and act = ? and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL) limit 0,1", array('favicon', 'photo_static'), 'fetch', 7200);
$logo = $cache->get("select id, photo, options from #_photo where type = ? and act = ? limit 0,1", array('logo', 'photo_static'), 'fetch', 7200);
$sloganheader = $d->rawQuery("select name$lang, text$lang from #_news where type = ? and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL) order by numb,id desc", array('slogan-header'));
$productListMenu = $d->rawQuery("select name$lang, slugvi, slugen, id from #_product_list where type = ? and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL) order by numb,id desc", array('san-pham'));
$duanListMenu = $d->rawQuery("select name$lang, slugvi, slugen, id from #_news_list where type = ? and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL) order by numb,id desc", array('du-an'));

// $banner = $cache->get("select photo from #_photo where type = ? and act = ? limit 0,1", array('banner', 'photo_static'), 'fetch', 7200);
// $dmca = $cache->get("select id, photo, status,link from #_photo where type = ? and act = ? limit 0,1", array('dmca', 'photo_static'), 'fetch', 7200);
// $bct = $cache->get("select id, photo, status,link from #_photo where type = ? and act = ? limit 0,1", array('bct', 'photo_static'), 'fetch', 7200);

$footer = $cache->get("select name$lang, content$lang,photo from #_static where type = ? limit 0,1", array('footer'), 'fetch', 7200);
// $qrzalo = $d->rawQueryOne("select name$lang, photo, idzalo, zalo from #_photo where type = ? and act = ? and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL) limit 0,1", array('zalo', 'photo_static'));

/* multi */
$tagsProduct = $d->rawQuery("select name$lang, slugvi, slugen, id from #_tags where type = ? and find_in_set('noibat',status) and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL) order by numb,id desc", array('san-pham'));
$tagsNews = $d->rawQuery("select name$lang, slugvi, slugen, id from #_tags where type = ? and find_in_set('noibat',status) and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL) order by numb,id desc", array('tin-tuc'));
$policy = $d->rawQuery("select name$lang, slugvi, slugen, id, photo from #_news where type = ? and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL) order by numb,id desc", array('chinh-sach'));
$purchaseProcess = $d->rawQuery("select name$lang, slugvi, slugen, id from #_news where type = ? and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL) order by numb,id desc", array('quy-trinh-mua-hang'));
$social = $d->rawQuery("select name$lang, photo, link from #_photo where type = ? and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL) order by numb,id desc", array('social'));
$socialfooter = $d->rawQuery("select name$lang, photo, link from #_photo where type = ? and find_in_set('hienthi',status) and (FIND_IN_SET('thungrac',status) = 0 OR status IS NULL) order by numb,id desc", array('social-footer'));


$hosonangluc = $d->rawQueryOne("select file_attach from #_static where type = ? limit 0,1", array('ho-so-nang-luc'));
/* Get statistic */
$counter = $statistic->getCounter();
$online = $statistic->getOnline();

/* Newsletter */
if (!empty($_POST['submit-newsletter'])) {
    $responseCaptcha = $_POST['recaptcha_response_newsletter'];
    $resultCaptcha = $func->checkRecaptcha($responseCaptcha);
    $scoreCaptcha = (!empty($resultCaptcha['score'])) ? $resultCaptcha['score'] : 0;
    $actionCaptcha = (!empty($resultCaptcha['action'])) ? $resultCaptcha['action'] : '';
    $testCaptcha = (!empty($resultCaptcha['test'])) ? $resultCaptcha['test'] : false;
    $dataNewsletter = (!empty($_POST['dataNewsletter'])) ? $_POST['dataNewsletter'] : null;

    /* Valid data */
    if (empty($dataNewsletter['fullname'])) {
        $flash->set('error', hotenkhongduoctrong);
    }

    if (empty($dataNewsletter['email'])) {
        $flash->set('error', emailkhongduoctrong);
    }

    if (!empty($dataNewsletter['email']) && !$func->isEmail($dataNewsletter['email'])) {
        $flash->set('error', emailkhonghople);
    }

    $error = $flash->get('error');

    if (!empty($error)) {
        $func->transfer($error, $configBase, false);
    }

    /* Save data */
    if (($scoreCaptcha >= 0.5 && $actionCaptcha == 'Newsletter') || $testCaptcha == true) {
        foreach ($dataNewsletter as $column => $value) {
            $dataNewsletter[$column] = htmlspecialchars($value);
        }

        $dataNewsletter['date_created'] = time();

        if ($d->insert('newsletter', $dataNewsletter)) {
            $func->transfer(dangkynhantinthanhcong, $configBase);
        } else {
            $func->transfer(dangkynhantinthatbai, $configBase, false);
        }
    } else {
        $func->transfer(dangkynhantinthatbai, $configBase, false);
    }
}
