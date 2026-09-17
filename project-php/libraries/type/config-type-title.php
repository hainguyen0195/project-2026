<?php

/* Title — thêm/bớt type tại đây
 * title   => name{lang}
 * title1  => text1{lang}
 * title2  => text2{lang}
 * title3  => text3{lang}
 * desc    => desc{lang}
 * desc_cke => bật CKEditor cho mô tả
 */

$nametype = "title-product-home";
$config['title'][$nametype]['title_main'] = "Tiêu đề sản phẩm";
$config['title'][$nametype]['check'] = array("hienthi" => hienthi);
$config['title'][$nametype]['title'] = true;
$config['title'][$nametype]['title1'] = false;
$config['title'][$nametype]['title2'] = false;
$config['title'][$nametype]['title3'] = false;
$config['title'][$nametype]['desc'] = true;
$config['title'][$nametype]['desc_cke'] = false;

$nametype = "title-service";
$config['title'][$nametype]['title_main'] = "Tiêu đề dịch vụ";
$config['title'][$nametype]['check'] = array("hienthi" => hienthi);
$config['title'][$nametype]['title'] = true;
$config['title'][$nametype]['title1'] = true;
$config['title'][$nametype]['title2'] = true;
$config['title'][$nametype]['title3'] = false;
$config['title'][$nametype]['desc'] = true;
$config['title'][$nametype]['desc_cke'] = false;

$nametype = "title-news";
$config['title'][$nametype]['title_main'] = "Tiêu đề tin tức";
$config['title'][$nametype]['check'] = array("hienthi" => hienthi);
$config['title'][$nametype]['title'] = true;
$config['title'][$nametype]['title1'] = true;
$config['title'][$nametype]['title2'] = false;
$config['title'][$nametype]['title3'] = false;
$config['title'][$nametype]['desc'] = true;
$config['title'][$nametype]['desc_cke'] = false;
