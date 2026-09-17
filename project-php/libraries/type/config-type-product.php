<?php
/* Sản phẩm */
/* These labels are normally defined by the admin language file. Frontend
 * loads this product config too, so provide safe fallbacks there. */
defined('sanpham') || define('sanpham', 'Sản phẩm');
defined('danhmuccap1') || define('danhmuccap1', 'Danh mục cấp 1');
defined('danhmuccap2') || define('danhmuccap2', 'Danh mục cấp 2');
defined('danhmuccap3') || define('danhmuccap3', 'Danh mục cấp 3');
defined('danhmuccap4') || define('danhmuccap4', 'Danh mục cấp 4');
defined('danhmuchang') || define('danhmuchang', 'Danh mục hãng');
defined('noibat') || define('noibat', 'Nổi bật');
defined('hienthi') || define('hienthi', 'Hiển thị');
defined('bestsaler') || define('bestsaler', 'Best Saler');
defined('hinhanhsanpham') || define('hinhanhsanpham', 'Hình ảnh sản phẩm');
defined('hinhanh') || define('hinhanh', 'Hình ảnh');
defined('thuvienanh') || define('thuvienanh', 'Thư viện ảnh');
defined('hinhanhthuvienanh') || define('hinhanhthuvienanh', 'Hình ảnh thư viện ảnh');

$nametype = "san-pham";
$config['product'][$nametype]['title_main'] = sanpham;
$config['product'][$nametype]['dropdown'] = true;
$config['product'][$nametype]['list'] = true;
$config['product'][$nametype]['cat'] = true;
$config['product'][$nametype]['item'] = true;
$config['product'][$nametype]['sub'] = false;
$config['product'][$nametype]['brand'] = true;
$config['product'][$nametype]['color'] = false;
$config['product'][$nametype]['size'] = true;
$config['product'][$nametype]['tags'] = false;
$config['product'][$nametype]['import'] = false;
$config['product'][$nametype]['export'] = false;
$config['product'][$nametype]['view'] = true;
$config['product'][$nametype]['copy'] = true;
$config['product'][$nametype]['copy_image'] = true;
$config['product'][$nametype]['comment'] = true;
$config['product'][$nametype]['slug'] = true;
$config['product'][$nametype]['check'] = array(
    'noibat' => noibat,
    'new' => 'Mới',
    'bestsaler' => bestsaler,
    'hienthi' => hienthi
);
$config['product'][$nametype]['images'] = true;
$config['product'][$nametype]['icon'] = false;
$config['product'][$nametype]['show_images'] = true;
$config['product'][$nametype]['gallery'] = array(
    $nametype => array(
        "title_main_photo" => hinhanhsanpham,
        "title_sub_photo" => hinhanh,
        "check_photo" => array("hienthi" => hienthi),
        "number_photo" => 3,
        "images_photo" => true,
        "cart_photo" => false,
        "avatar_photo" => true,
        "name_photo" => true,
        "width_photo" => 540,
        "height_photo" => 540,
        "thumb_photo" => '100x100x1',
        "img_type_photo" => '.jpg|.gif|.png|.jpeg|.webp|.WEBP'
    )
);
$config['product'][$nametype]['code'] = true;
$config['product'][$nametype]['regular_price'] = true;
$config['product'][$nametype]['sale_price'] = true;
$config['product'][$nametype]['discount'] = true;
$config['product'][$nametype]['desc'] = true;
$config['product'][$nametype]['desc_cke'] = true;
$config['product'][$nametype]['content'] = true;
$config['product'][$nametype]['content_cke'] = true;
$config['product'][$nametype]['loiich'] = false;
$config['product'][$nametype]['loiich_cke'] = false;
$config['product'][$nametype]['thongsokythuat'] = true;
$config['product'][$nametype]['thongsokythuat_cke'] = true;
$config['product'][$nametype]['baogia'] = false;
$config['product'][$nametype]['baogia_cke'] = false;
$config['product'][$nametype]['schema'] = true;
$config['product'][$nametype]['seo'] = true;
$config['product'][$nametype]['width'] = 450;
$config['product'][$nametype]['height'] = 450;
$config['product'][$nametype]['thumb'] = '450x450x1';
$config['product'][$nametype]['width_icon'] = 370;
$config['product'][$nametype]['height_icon'] = 370;
$config['product'][$nametype]['thumb_icon'] = '370x370x1';
$config['product'][$nametype]['img_type'] = '.jpg|.gif|.png|.jpeg|.webp|.WEBP';

/* Sản phẩm (List) */
$config['product'][$nametype]['title_main_list'] = danhmuccap1;
$config['product'][$nametype]['images_list'] = true;
$config['product'][$nametype]['show_images_list'] = true;
$config['product'][$nametype]['slug_list'] = true;
$config['product'][$nametype]['check_list'] = array("noibat" => noibat, "hienthi" => hienthi);
$config['product'][$nametype]['desc_list'] = false;
$config['product'][$nametype]['desc_cke_list'] = false;
$config['product'][$nametype]['seo_list'] = true;
$config['product'][$nametype]['view_list'] = true;
$config['product'][$nametype]['copy_list'] = true;
$config['product'][$nametype]['copy_image_list'] = true;
$config['product'][$nametype]['width_list'] = 1440;
$config['product'][$nametype]['height_list'] = 1920;
$config['product'][$nametype]['thumb_list'] = '1200x540x1';
$config['product'][$nametype]['img_type_list'] = '.jpg|.gif|.png|.jpeg|.webp|.WEBP';

/* Sản phẩm (Cat) */
$config['product'][$nametype]['title_main_cat'] = danhmuccap2;
$config['product'][$nametype]['images_cat'] = false;
$config['product'][$nametype]['show_images_cat'] = false;
$config['product'][$nametype]['slug_cat'] = true;
$config['product'][$nametype]['check_cat'] = array("hienthi" => hienthi);
$config['product'][$nametype]['desc_cat'] = false;
$config['product'][$nametype]['seo_cat'] = true;
$config['product'][$nametype]['view_cat'] = true;
$config['product'][$nametype]['copy_cat'] = true;
$config['product'][$nametype]['copy_image_cat'] = true;
$config['product'][$nametype]['width_cat'] = 300;
$config['product'][$nametype]['height_cat'] = 200;
$config['product'][$nametype]['thumb_cat'] = '100x100x1';
$config['product'][$nametype]['img_type_cat'] = '.jpg|.gif|.png|.jpeg|.webp|.WEBP';

/* Sản phẩm (Item) */
$config['product'][$nametype]['title_main_item'] = danhmuccap3;
$config['product'][$nametype]['images_item'] = false;
$config['product'][$nametype]['show_images_item'] = false;
$config['product'][$nametype]['slug_item'] = true;
$config['product'][$nametype]['check_item'] = array("hienthi" => hienthi);
$config['product'][$nametype]['desc_item'] = false;
$config['product'][$nametype]['seo_item'] = true;
$config['product'][$nametype]['width_item'] = 300;
$config['product'][$nametype]['height_item'] = 200;
$config['product'][$nametype]['thumb_item'] = '100x100x1';
$config['product'][$nametype]['img_type_item'] = '.jpg|.gif|.png|.jpeg|.webp|.WEBP';

/* Sản phẩm (Sub) */
// $config['product'][$nametype]['title_main_sub'] = danhmuccap4;
// $config['product'][$nametype]['images_sub'] = true;
// $config['product'][$nametype]['show_images_sub'] = true;
// $config['product'][$nametype]['slug_sub'] = true;
// $config['product'][$nametype]['check_sub'] = array("noibat" => noibat, "hienthi" => hienthi);
// $config['product'][$nametype]['desc_sub'] = true;
// $config['product'][$nametype]['seo_sub'] = true;
// $config['product'][$nametype]['width_sub'] = 300;
// $config['product'][$nametype]['height_sub'] = 200;
// $config['product'][$nametype]['thumb_sub'] = '100x100x1';
// $config['product'][$nametype]['img_type_sub'] = '.jpg|.gif|.png|.jpeg|.webp|.WEBP';

/* Sản phẩm (Hãng) */
$config['product'][$nametype]['title_main_brand'] = danhmuchang;
$config['product'][$nametype]['images_brand'] = false;
$config['product'][$nametype]['show_images_brand'] = false;
$config['product'][$nametype]['slug_brand'] = true;
$config['product'][$nametype]['check_brand'] = array("noibat" => noibat, "hienthi" => hienthi);
$config['product'][$nametype]['seo_brand'] = true;
$config['product'][$nametype]['width_brand'] = 150;
$config['product'][$nametype]['height_brand'] = 150;
$config['product'][$nametype]['thumb_brand'] = '100x100x1';
$config['product'][$nametype]['img_type_brand'] = '.jpg|.gif|.png|.jpeg|.webp|.WEBP';

/* Sản phẩm (Size) */
// $config['product'][$nametype]['check_size'] = array("hienthi" => hienthi);

/* Sản phẩm (Color) */
// $config['product'][$nametype]['check_color'] = array("hienthi" => hienthi);
// $config['product'][$nametype]['color_images'] = true;
// $config['product'][$nametype]['color_code'] = true;
// $config['product'][$nametype]['color_type'] = true;
// $config['product'][$nametype]['width_color'] = 30;
// $config['product'][$nametype]['height_color'] = 30;
// $config['product'][$nametype]['thumb_color'] = '100x100x1';
// $config['product'][$nametype]['img_type_color'] = '.jpg|.gif|.png|.jpeg|.webp|.WEBP';

// /* Thư viện ảnh */
// $nametype = "thu-vien-anh";
// $config['product'][$nametype]['title_main'] = thuvienanh;
// $config['product'][$nametype]['check'] = array("hienthi" => hienthi);
// $config['product'][$nametype]['view'] = true;
// $config['product'][$nametype]['copy'] = true;
// $config['product'][$nametype]['slug'] = true;
// $config['product'][$nametype]['images'] = true;
// $config['product'][$nametype]['show_images'] = true;
// $config['product'][$nametype]['gallery'] = array(
//     $nametype => array(
//         "title_main_photo" => hinhanhthuvienanh,
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
// $config['product'][$nametype]['seo'] = true;
// $config['product'][$nametype]['width'] = 270;
// $config['product'][$nametype]['height'] = 270;
// $config['product'][$nametype]['thumb'] = '100x100x1';
// $config['product'][$nametype]['img_type'] = '.jpg|.gif|.png|.jpeg|.webp|.WEBP';
