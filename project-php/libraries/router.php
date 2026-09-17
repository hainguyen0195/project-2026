<?php
/* Check HTTP */
$func->checkHTTP($http, $config['arrayDomainSSL'], $configBase, $configUrl);

/* Validate URL */
$func->checkUrl($config['website']['index']);

/* Check login */
$func->checkLoginMember();

if (!empty($config['website']['linkredirect'])) {
    $func->checkRedirect();
}

/* Mobile detect */
$deviceType = ($detect->isMobile() || $detect->isTablet()) ? 'mobile' : 'computer';
$isIOS = $detect->isIOS();
define('TEMPLATE', ($deviceType == 'computer') ? './templates/' : './templates/');

/* Router */
$router->setBasePath($config['database']['url']);
$router->map('GET', ADMIN . '/', function () {
    global $func, $config;
    $func->redirect($config['database']['url'] . ADMIN . "/index.php");
    exit;
});
$router->map('GET', ADMIN, function () {
    global $func, $config;
    $func->redirect($config['database']['url'] . ADMIN . "/index.php");
    exit;
});
$router->map('GET|POST', '', 'index', 'home');
$router->map('GET|POST', 'index.php', 'index', 'index');
$router->map('GET|POST', 'sitemap.xml', 'sitemap', 'sitemap');
$router->map('GET|POST', '[a:com]', 'allpage', 'show');
$router->map('GET|POST', '[a:com]/[a:lang]/', 'allpagelang', 'lang');
$router->map('GET|POST', '[a:com]/[a:action]', 'account', 'account');
$router->map('GET', THUMBS . '/[i:w]x[i:h]x[i:z]/[**:src]', function ($w, $h, $z, $src) {
    global $func;
    $infoWebp = $func->webpinfo($src);
    if (isset($infoWebp['Animation']) && $infoWebp['Animation'] === true) {
        $contentWebp = file_get_contents($src);
        header('Content-Type: image/webp');
        echo $contentWebp;
    } else {
        $func->createThumb($w, $h, $z, $src, null, THUMBS);
    }
}, 'thumb');
$router->map('GET', WATERMARK . '/product/[i:w]x[i:h]x[i:z]/[**:src]', function ($w, $h, $z, $src) {
    global $func, $d;
    $wtm = $d->rawQueryOne("select status, photo, options from #_photo where type = ? and act = ? limit 0,1", array('watermark', 'photo_static'));
    $func->createThumb($w, $h, $z, $src, $wtm, "product");
}, 'watermark');

$router->map('GET', WATERMARK . '/news/[i:w]x[i:h]x[i:z]/[**:src]', function ($w, $h, $z, $src) {
    global $func, $d;
    $wtm = $d->rawQueryOne("select status, photo, options from #_photo where type = ? and act = ? limit 0,1", array('watermark-news', 'photo_static'));
    $func->createThumb($w, $h, $z, $src, $wtm, "news");
}, 'watermarkNews');


/* Router match */
$match = $router->match();

/* Router check */
if (is_array($match)) {
    if (is_callable($match['target'])) {
        call_user_func_array($match['target'], $match['params']);
    } else {
        $com = (!empty($match['params']['com'])) ? htmlspecialchars($match['params']['com']) : htmlspecialchars($match['target']);
        $getPage = !empty($_GET['p']) ? htmlspecialchars($_GET['p']) : 1;
    }
} else {
    header('HTTP/1.0 404 Not Found', true, 404);
    include("404.php");
    exit;
}

/* Setting */
$sqlCache = "select * from #_setting";
$setting = $cache->get($sqlCache, null, 'fetch', 7200);
$optsetting = (!empty($setting['options'])) ? json_decode($setting['options'], true) : null;

/* Lang */
if (!empty($match['params']['lang'])) $_SESSION['lang'] = $match['params']['lang'];
else if (empty($_SESSION['lang']) && empty($match['params']['lang'])) $_SESSION['lang'] = $optsetting['lang_default'] ? $optsetting['lang_default'] : 'vi';
$lang = $_SESSION['lang'];

/* Check lang */
$weblang = (!empty($config['website']['lang'])) ? array_keys($config['website']['lang']) : array();

if (!in_array($lang, $weblang)) {
    $_SESSION['lang'] = 'vi';
    $lang = $_SESSION['lang'];
}

/* Slug lang */
$sluglang = 'slugvi';

/* SEO Lang */
$seolang = "vi";

/* Require datas lang */
require_once LIBRARIES . "lang/web/$lang.php";

/* Product settings are also used by frontend sources/templates. */
require_once LIBRARIES . "type/config-type-product.php";

/* Tối ưu link */
$requick = array(
    /* Sản phẩm */
    array("tbl" => "product_list", "field" => "idl", "source" => "product", "com" => "san-pham", "type" => "san-pham"),
    array("tbl" => "product_cat", "field" => "idc", "source" => "product", "com" => "san-pham", "type" => "san-pham"),
    array("tbl" => "product_item", "field" => "idi", "source" => "product", "com" => "san-pham", "type" => "san-pham"),
    array("tbl" => "product_sub", "field" => "ids", "source" => "product", "com" => "san-pham", "type" => "san-pham"),
    array("tbl" => "product_brand", "field" => "idb", "source" => "product", "com" => "thuong-hieu", "type" => "san-pham"),
    array("tbl" => "product", "field" => "id", "source" => "product", "com" => "san-pham", "type" => "san-pham", "menu" => true),

    /* Tags */
    // array("tbl" => "tags", "tbltag" => "product", "field" => "id", "source" => "tags", "com" => "tags-san-pham", "type" => "san-pham", "menu" => true),
    // array("tbl" => "tags", "tbltag" => "news", "field" => "id", "source" => "tags", "com" => "tags-tin-tuc", "type" => "tin-tuc", "menu" => true),

    /* Thư viện ảnh */
    // array("tbl" => "product", "field" => "id", "source" => "product", "com" => "thu-vien-anh", "type" => "thu-vien-anh", "menu" => true),

    /* Video */
    array("tbl" => "photo", "field" => "id", "source" => "video", "com" => "video", "type" => "video", "menu" => true),

    /* Tin tức */
    array("tbl" => "news_list", "field" => "idl", "source" => "news", "com" => "tin-tuc", "type" => "tin-tuc"),
    // array("tbl" => "news_cat", "field" => "idc", "source" => "news", "com" => "tin-tuc", "type" => "tin-tuc"),
    // array("tbl" => "news_item", "field" => "idi", "source" => "news", "com" => "tin-tuc", "type" => "tin-tuc"),
    // array("tbl" => "news_sub", "field" => "ids", "source" => "news", "com" => "tin-tuc", "type" => "tin-tuc"),
    array("tbl" => "news", "field" => "id", "source" => "news", "com" => "tin-tuc", "type" => "tin-tuc", "menu" => true),
    array("tbl" => "news_list", "field" => "idl", "source" => "news", "com" => "blog", "type" => "blog"),
    array("tbl" => "news", "field" => "id", "source" => "news", "com" => "blog", "type" => "blog", "menu" => true),

    /* Bài viết */
    array("tbl" => "news", "field" => "id", "source" => "news", "com" => "tuyen-dung", "type" => "tuyen-dung", "menu" => true),
    array("tbl" => "news_list", "field" => "idl", "source" => "project", "com" => "du-an", "type" => "du-an"),
    array("tbl" => "news", "field" => "id", "source" => "news", "com" => "du-an", "type" => "du-an", "menu" => true),
    array("tbl" => "news", "field" => "id", "source" => "news", "com" => "chinh-sach", "type" => "chinh-sach", "menu" => false),
    array("tbl" => "news", "field" => "id", "source" => "news", "com" => "quy-trinh-mua-hang", "type" => "quy-trinh-mua-hang", "menu" => false),

    /* Trang tĩnh */
    array("tbl" => "static", "field" => "id", "source" => "static", "com" => "gioi-thieu", "type" => "gioi-thieu", "menu" => true),
    array("tbl" => "static", "field" => "id", "source" => "static", "com" => "ve-comi", "type" => "ve-comi", "menu" => true),
    // array("tbl" => "static", "field" => "id", "source" => "static", "com" => "catalog", "type" => "catalog", "menu" => true),

    /* Liên hệ */
    array("tbl" => "", "field" => "id", "source" => "", "com" => "lien-he", "type" => "", "menu" => true),

    /* Booking */
    array("tbl" => "", "field" => "id", "source" => "", "com" => "booking", "type" => "", "menu" => false),
);

/* Find data */
if (!empty($com) && !in_array($com, ['tim-kiem', 'account', 'sitemap'])) {
    foreach ($requick as $k => $v) {
        $urlTbl = (!empty($v['tbl'])) ? $v['tbl'] : '';
        $urlTblTag = (!empty($v['tbltag'])) ? $v['tbltag'] : '';
        $urlType = (!empty($v['type'])) ? $v['type'] : '';
        $urlField = (!empty($v['field'])) ? $v['field'] : '';
        $urlCom = (!empty($v['com'])) ? $v['com'] : '';

        if (!empty($urlTbl) && !in_array($urlTbl, ['static', 'photo'])) {
            $row = $d->rawQueryOne("select id from #_$urlTbl where $sluglang = ? and type = ? and find_in_set('hienthi',status) limit 0,1", array($com, $urlType));

            if (!empty($row['id'])) {
                $_GET[$urlField] = $row['id'];
                $com = $urlCom;
                break;
            }
        }
    }
}

/* Switch coms */
switch ($com) {
    case 'lien-he':
        $source = "contact";
        $template = "contact/contact";
        $seo->set('type', 'object');
        $titleMain = lienhe;
        break;

    case 'gioi-thieu':
        $source = "about";
        $template = "static/about";
        $type = $com;
        $seo->set('type', 'article');
        $titleMain = gioithieu;
        break;

    case 've-comi':
        $source = "about";
        $template = "static/about";
        $type = $com;
        $seo->set('type', 'article');
        $titleMain = vecomi;
        break;

    case 'catalog':
        $source = "static";
        $template = "static/static";
        $type = 'catalog';
        $seo->set('type', 'article');
        $titleMain = "Catalog";
        break;

    case 'blog':
        $source = "news";
        $template = isset($_GET['id']) ? "news/news_detail" : "news/blog";
        $seo->set('type', isset($_GET['id']) ? "article" : "object");
        $type = $com;
        $titleMain = blog;
        break;
    case 'giai-phap':
        $source = "news";
        $template = isset($_GET['id']) ? "solution/solution_detail" : "solution/solution";
        $seo->set('type', isset($_GET['id']) ? "article" : "object");
        $type = $com;
        $titleMain = giaiphap;
        break;
    case 'du-an':
        $source = "project";
        $template = isset($_GET['id']) ? "project/project_detail" : "project/project";
        $seo->set('type', isset($_GET['id']) ? "article" : "object");
        $type = $com;
        $titleMain = duan;
        break;

    case 'tuyen-dung':
        $source = "recruitment";
        $template = isset($_GET['id']) ? "recruitment/recruitment_detail" : "recruitment/recruitment";
        $seo->set('type', isset($_GET['id']) ? "article" : "object");
        $type = $com;
        $titleMain = tuyendung;
        break;

    case 'chinh-sach':
    case 'quy-trinh-mua-hang':
        $source = "news";
        $template = isset($_GET['id']) ? "news/news_detail" : "";
        $seo->set('type', 'article');
        $type = $com;
        $titleMain = null;
        break;

    case 'thuong-hieu':
        $source = "product";
        $template = "product/product";
        $seo->set('type', 'object');
        $type = 'san-pham';
        $titleMain = null;
        break;

    case 'best-saler':
        $source = "bestsaler";
        $template = "product/bestsaler";
        $seo->set('type', 'object');
        $type = 'san-pham';
        $titleMain = "Best Sellers";
        break;

    case 'flash-sale':
        $source = "flashsale";
        $template = "product/flashsale";
        $seo->set('type', 'object');
        $type = 'san-pham';
        $titleMain = "Flash Sale";
        break;

    case 'san-pham':
        $source = "product";
        $template = isset($_GET['id']) ? "product/product_detail" : "product/product";
        $seo->set('type', isset($_GET['id']) ? "article" : "object");
        $type = $com;
        $titleMain = sanpham;
        break;

    case 'tim-kiem':
        $source = "search";
        $template = "product/product";
        $seo->set('type', 'object');
        $titleMain = timkiem;
        break;

    case 'tags-san-pham':
        $source = "tags";
        $template = "product/product";
        $type = $urlType;
        $table = $urlTblTag;
        $seo->set('type', 'object');
        $titleMain = null;
        break;

    case 'tags-tin-tuc':
        $source = "tags";
        $template = "news/news";
        $type = $urlType;
        $table = $urlTblTag;
        $seo->set('type', 'object');
        $titleMain = null;
        break;

    case 'thu-vien-anh':
        $source = "product";
        $template = isset($_GET['id']) ? "album/album_detail" : "album/album";
        $seo->set('type', isset($_GET['id']) ? "article" : "object");
        $type = $com;
        $titleMain = thuvienanh;
        break;

    case 'video':
        $source = "video";
        $template = "video/video";
        $type = $com;
        $seo->set('type', 'object');
        $titleMain = "Video";
        break;

    case 'gio-hang':
        $source = "order";
        $template = 'order/order';
        $titleMain = giohang;
        $seo->set('type', 'object');
        break;

    case 'account':
        $source = "user";
        break;

    case 'ngon-ngu':
        if (isset($lang)) {
            switch ($lang) {
                case 'vi':
                    $_SESSION['lang'] = 'vi';
                    break;
                case 'en':
                    $_SESSION['lang'] = 'en';
                    break;
                default:
                    $_SESSION['lang'] = 'vi';
                    break;
            }
        }
        $func->redirect($_SERVER['HTTP_REFERER']);
        break;

    case 'sitemap':
        include_once LIBRARIES . "sitemap.php";
        exit();

    case '':
    case 'index':
        $titleMain = "";
        $source = "index";
        $template = "index/index";
        $seo->set('type', 'website');
        break;

    default:
        header('HTTP/1.0 404 Not Found', true, 404);
        include("404.php");
        exit();
}

/* Require datas for all page */
require_once SOURCES . "allpage.php";

/* Include sources */
if (!empty($source)) {
    include SOURCES . $source . ".php";
}

/* Include sources */
if (empty($template)) {
    header('HTTP/1.0 404 Not Found', true, 404);
    include("404.php");
    exit();
}
