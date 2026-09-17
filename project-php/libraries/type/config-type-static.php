<?php

/* Banner quảng cáo trang chủ */
$nametype = "banner-quang-cao";
$config['static'][$nametype]['title_main'] = "Banner quảng cáo";
$config['static'][$nametype]['check'] = array("hienthi" => hienthi);
$config['static'][$nametype]['name'] = true;
$config['static'][$nametype]['name_label'] = "Dòng ưu đãi (Ví dụ: 25% OFF)";
$config['static'][$nametype]['text1'] = true;
$config['static'][$nametype]['text1_label'] = "Tiêu đề lớn";
$config['static'][$nametype]['desc'] = true;
$config['static'][$nametype]['desc_cke'] = false;
$config['static'][$nametype]['text2'] = true;
$config['static'][$nametype]['text2_label'] = "Chữ trên nút";
$config['static'][$nametype]['text3'] = true;
$config['static'][$nametype]['text3_label'] = "Link nút";
$config['static'][$nametype]['images'] = true;
$config['static'][$nametype]['width'] = 1700;
$config['static'][$nametype]['height'] = 550;
$config['static'][$nametype]['img_type'] = '.jpg|.gif|.png|.jpeg|.webp|.WEBP';

/* Sản phẩm mới trang chủ */
$nametype = "san-pham-moi";
$config['static'][$nametype]['title_main'] = "Sản phẩm mới";
$config['static'][$nametype]['check'] = array("hienthi" => hienthi);
$config['static'][$nametype]['name'] = true;
$config['static'][$nametype]['name_label'] = "Tiêu đề nhỏ";
$config['static'][$nametype]['text1'] = true;
$config['static'][$nametype]['text1_label'] = "Chữ 1";
$config['static'][$nametype]['text2'] = true;
$config['static'][$nametype]['text2_label'] = "Chữ 2";
$config['static'][$nametype]['text3'] = true;
$config['static'][$nametype]['text3_label'] = "Chữ 3";
$config['static'][$nametype]['tieuchi'] = true;
$config['static'][$nametype]['tieuchi_value'] = false;
$config['static'][$nametype]['tieuchi_title'] = "Nhãn sản phẩm";
$config['static'][$nametype]['tieuchi_placeholder'] = "Ví dụ: Chính hãng";
$config['static'][$nametype]['images'] = true;
$config['static'][$nametype]['product_select'] = true;
$config['static'][$nametype]['width'] = 770;
$config['static'][$nametype]['height'] = 770;
$config['static'][$nametype]['img_type'] = '.jpg|.gif|.png|.jpeg|.webp|.WEBP';

/* Giới thiệu */
// $nametype = "thu-ngo";
// $config['static'][$nametype]['title_main'] = "Thư Ngỏ";
// $config['static'][$nametype]['check'] = array("hienthi" => hienthi);
// $config['static'][$nametype]['images'] = false;
// $config['static'][$nametype]['images1'] = false;
// $config['static'][$nametype]['file'] = false;
// $config['static'][$nametype]['name'] = true;
// $config['static'][$nametype]['desc'] = true;
// $config['static'][$nametype]['desc_cke'] = true;
// $config['static'][$nametype]['content'] = false;
// $config['static'][$nametype]['content_cke'] = false;
// $config['static'][$nametype]['seo'] = false;
// $config['static'][$nametype]['video'] = false;
// $config['static'][$nametype]['tieuchi'] = false;
// $config['static'][$nametype]['tieuchi_value'] = false; // true: hiện ô value, false: chỉ hiện nội dung tiêu chí
// $config['static'][$nametype]['width'] = 300;
// $config['static'][$nametype]['height'] = 200;
// $config['static'][$nametype]['width1'] = 300;
// $config['static'][$nametype]['height1'] = 200;
// // $config['static'][$nametype]['gallery'] = array(
// //     $nametype => array(
// //         "title_main_photo" => hinhanh,
// //         "title_sub_photo" => hinhanh,
// //         "check_photo" => array("hienthi" => hienthi),
// //         "number_photo" => 2,
// //         "images_photo" => true,
// //         "avatar_photo" => true,
// //         "name_photo" => true,
// //         "width_photo" => 540,
// //         "height_photo" => 540,
// //         "thumb_photo" => '100x100x1',
// //         "img_type_photo" => '.jpg|.gif|.png|.jpeg|.webp|.WEBP'
// //     )
// // );
// $config['static'][$nametype]['img_type'] = '.jpg|.gif|.png|.jpeg|.webp|.WEBP';
// $config['static'][$nametype]['video_type'] = '.mp4';
// $config['static'][$nametype]['file_type'] = '.doc|.docx|.pdf|.rar|.zip|.ppt|.pptx|.xls|.xlsx|.jpg|.png|.gif|.webp|.WEBP';


/* Giới thiệu */
$nametype = "gioi-thieu";
$config['static'][$nametype]['title_main'] = gioithieu;
$config['static'][$nametype]['check'] = array("hienthi" => hienthi);
$config['static'][$nametype]['images'] = false;
$config['static'][$nametype]['images1'] = false;
$config['static'][$nametype]['file'] = false;
$config['static'][$nametype]['tieuchi'] = true;
$config['static'][$nametype]['tieuchi_value'] = false;
$config['static'][$nametype]['name'] = true;
$config['static'][$nametype]['desc'] = true;
$config['static'][$nametype]['desc_cke'] = true;
$config['static'][$nametype]['content'] = true;
$config['static'][$nametype]['content_cke'] = true;
$config['static'][$nametype]['seo'] = true;
$config['static'][$nametype]['video'] = false;
// $config['static'][$nametype]['articles'] = array(
//     'title_main' => 'Bài viết giới thiệu',
//     'type' => 'static-gioi-thieu',
//     'name' => true,
//     'number' => true,
//     'unit' => true,
//     'text' => true,
//     'desc' => true,
//     'desc_cke' => false,
//     'content' => false,
//     'content_cke' => false,
//     'images' => true,
//     'width' => 800,
//     'height' => 600,
//     'img_type' => '.jpg|.gif|.png|.jpeg|.webp|.WEBP'
// );
$config['static'][$nametype]['width'] = 300;
$config['static'][$nametype]['height'] = 200;
$config['static'][$nametype]['width1'] = 300;
$config['static'][$nametype]['height1'] = 200;
// $config['static'][$nametype]['gallery'] = array(
//     $nametype => array(
//         "title_main_photo" => hinhanh,
//         "title_sub_photo" => hinhanh,
//         "check_photo" => array("hienthi" => hienthi),
//         "number_photo" => 2,
//         "images_photo" => true,
//         "avatar_photo" => true,
//         "name_photo" => true,
//         "width_photo" => 540,
//         "height_photo" => 540,
//         "thumb_photo" => '100x100x1',
//         "img_type_photo" => '.jpg|.gif|.png|.jpeg|.webp|.WEBP'
//     )
// );
$config['static'][$nametype]['img_type'] = '.jpg|.gif|.png|.jpeg|.webp|.WEBP';
$config['static'][$nametype]['video_type'] = '.mp4';
$config['static'][$nametype]['file_type'] = '.doc|.docx|.pdf|.rar|.zip|.ppt|.pptx|.xls|.xlsx|.jpg|.png|.gif|.webp|.WEBP';

/* Hồ sơ năng lực */
// $nametype = "ho-so-nang-luc";
// $config['static'][$nametype]['title_main'] = "Hồ sơ năng lực";
// // $config['static'][$nametype]['check'] = array("hienthi" => hienthi);
// $config['static'][$nametype]['images'] = false;
// $config['static'][$nametype]['images1'] = false;
// $config['static'][$nametype]['file'] = true;
// $config['static'][$nametype]['name'] = true;
// $config['static'][$nametype]['desc'] = false;
// $config['static'][$nametype]['desc_cke'] = false;
// $config['static'][$nametype]['content'] = false;
// $config['static'][$nametype]['content_cke'] = false;
// $config['static'][$nametype]['seo'] = false;
// $config['static'][$nametype]['video'] = false;
// $config['static'][$nametype]['width'] = 300;
// $config['static'][$nametype]['height'] = 200;
// $config['static'][$nametype]['width1'] = 300;
// $config['static'][$nametype]['height1'] = 200;
// $config['static'][$nametype]['img_type'] = '.jpg|.gif|.png|.jpeg|.webp|.WEBP';
// $config['static'][$nametype]['file_type'] = '.doc|.docx|.pdf|.rar|.zip|.ppt|.pptx|.xls|.xlsx|.jpg|.png|.gif|.webp|.WEBP';

/* catalog */
// $nametype = "catalog";
// $config['static'][$nametype]['title_main'] = "catalog";
// $config['static'][$nametype]['check'] = array("hienthi" => hienthi);
// $config['static'][$nametype]['images'] = true;
// $config['static'][$nametype]['images1'] = false;
// $config['static'][$nametype]['file'] = false;
// $config['static'][$nametype]['name'] = true;
// $config['static'][$nametype]['desc'] = true;
// $config['static'][$nametype]['desc_cke'] = true;
// $config['static'][$nametype]['content'] = true;
// $config['static'][$nametype]['content_cke'] = true;
// $config['static'][$nametype]['seo'] = true;
// $config['static'][$nametype]['video'] = false;
// $config['static'][$nametype]['width'] = 300;
// $config['static'][$nametype]['height'] = 200;
// $config['static'][$nametype]['width1'] = 300;
// $config['static'][$nametype]['height1'] = 200;

// $config['static'][$nametype]['img_type'] = '.jpg|.gif|.png|.jpeg|.webp|.WEBP';
// $config['static'][$nametype]['video_type'] = '.mp4';
// $config['static'][$nametype]['file_type'] = '.doc|.docx|.pdf|.rar|.zip|.ppt|.pptx|.xls|.xlsx|.jpg|.png|.gif|.webp|.WEBP';


/* Liên hệ */
$nametype = "lienhe";
$config['static'][$nametype]['title_main'] = lienhe;
$config['static'][$nametype]['check'] = array("hienthi" => hienthi);
$config['static'][$nametype]['text1'] = true;
$config['static'][$nametype]['text2'] = true;
$config['static'][$nametype]['text3'] = true;
$config['static'][$nametype]['desc'] = false;
$config['static'][$nametype]['tieuchi'] = true;
$config['static'][$nametype]['tieuchi_value'] = false;
$config['static'][$nametype]['content'] = true;
$config['static'][$nametype]['content_cke'] = true;

/* Footer */
$nametype = "footer";
$config['static'][$nametype]['title_main'] = "Footer";
$config['static'][$nametype]['check'] = array("hienthi" => hienthi);
$config['static'][$nametype]['name'] = false;
$config['static'][$nametype]['content'] = true;
$config['static'][$nametype]['content_cke'] = true;
$config['static'][$nametype]['images'] = true;
$config['static'][$nametype]['width'] = 300;
$config['static'][$nametype]['height'] = 200;
$config['static'][$nametype]['img_type'] = '.jpg|.gif|.png|.jpeg|.webp|.WEBP';
