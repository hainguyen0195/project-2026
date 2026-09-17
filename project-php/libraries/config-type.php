<?php
/* Config type - Group */
// $config['group'] = array(
//     groupsanpham => array(
//         "product" => array("san-pham"),
//         "tags" => array("san-pham"),
//         "photo_static" => array("watermark"),
//     ),
//     grouptintuc => array(
//         "news" => array("tin-tuc", "tuyen-dung"),
//         "tags" => array("tin-tuc"),
//         "photo_static" => array("watermark-news"),
//     )
// );

/* Config type - Product */
require_once LIBRARIES . 'type/config-type-product.php';


/* Config type - News */
require_once LIBRARIES . 'type/config-type-news.php';

/* Config type - Static */
require_once LIBRARIES . 'type/config-type-static.php';

/* Config type - Title */
require_once LIBRARIES . 'type/config-type-title.php';

/* Config type - Background */
require_once LIBRARIES . 'type/config-type-background.php';

/* Config type - Photo */
require_once LIBRARIES . 'type/config-type-photo.php';

/* Seo page */
$config['seopage']['page'] = array(
    "trang-chu" => trangchu,
    "san-pham" => sanpham,
    "tin-tuc" => tintuc,
    "du-an" => duan,
    "tuyen-dung" => tuyendung,
    "lien-he" => lienhe
);
$config['seopage']['width'] = 300;
$config['seopage']['height'] = 200;
$config['seopage']['thumb'] = '300x200x1';
$config['seopage']['img_type'] = '.jpg|.gif|.png|.jpeg|.gif|.webp|.WEBP';

/* Setting */
$config['setting']['slogan'] = true;
$config['setting']['copyright'] = true;
$config['setting']['social'] = true;
$config['setting']['keysearch'] = true;
$config['setting']['address'] = true;
$config['setting']['worktime'] = false;
$config['setting']['phone'] = false;
$config['setting']['hotline'] = true;
$config['setting']['zalo'] = false;
$config['setting']['oaidzalo'] = false;
$config['setting']['email'] = true;
$config['setting']['website'] = true;
$config['setting']['fanpage'] = true;
$config['setting']['coords'] = true;
$config['setting']['coords_iframe'] = true;
$config['setting']['link_googlemaps'] = true;


/* Quản lý liên lệ */
$config['contact']['check'] = array("hienthi" => xacnhan);


if ($config['website']['linkredirect'] == true) {
    $config['photo']['man_photo']['dieuhuonglink']['title_main_photo'] = linkredirect;
    $config['photo']['man_photo']['dieuhuonglink']['check_photo'] = array("hienthi" => hienthi);
    $config['photo']['man_photo']['dieuhuonglink']['number_photo'] = 5;
    $config['photo']['man_photo']['dieuhuonglink']['loaidieuhuong_photo'] = true;
    $config['photo']['man_photo']['dieuhuonglink']['link_photo'] = true;
    $config['photo']['man_photo']['dieuhuonglink']['link2_photo'] = true;
}
