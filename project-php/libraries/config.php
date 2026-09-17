<?php
if (!defined('LIBRARIES')) die("Error");

/* Timezone */
date_default_timezone_set('Asia/Ho_Chi_Minh');

/* Cấu hình coder */
define('NN_CONTRACT', 'MSHD');
define('NN_AUTHOR', 'hainguyen0195@gmail.com');

/* Cấu hình chung */
/* PHP Ver: 8.4  */
$config = array(
    'author' => array(
        'name' => 'Hai Nguyen',
        'email' => 'hainguyen0195@gmail.com',
        'timefinish' => '21/07/2026'
    ),
    'arrayDomainSSL' => array("demo.hainguyen7.website"),
    'database' => array(
        'server-name' => $_SERVER["SERVER_NAME"],
        'url' => '/src-moi-2026/',
        'type' => 'mysql',
        'host' => 'localhost',
        'username' => 'hainguyen_databasesrc2026',
        'password' => 'databasesrc2026',
        'dbname' => 'hainguyen_databasesrc2026',
        'port' => 3306,
        'prefix' => 'table_',
        'charset' => 'utf8mb4'
    ),
    'website' => array(
        'error-reporting' => 0,
        'secret' => '$cris@',
        'salt' => '@hcr7f#@%21%',
        'debug-developer' => true,
        'debug-css' => true,
        'debug-js' => true,
        'index' => false,
        'linkredirect' => false,
        // Enable the product facts editor and dynamic Product JSON-LD output.
        'ai_product' => true,
        // Enable the article facts editor, visible AI context and dynamic Article JSON-LD output.
        'ai_article' => true,
        'image' => array(),
        'noseo' => array('user', 'order', 'search'), //source
        'video' => array(
            'extension' => array('mp4', 'mkv'),
            'poster' => array(
                'width' => 700,
                'height' => 610,
                'extension' => '.jpg|.png|.jpeg'
            ),
            'allow-size' => '100Mb',
            'max-size' => 100 * 1024 * 1024
        ),
        'upload' => array(
            'max-width' => 2520,
            'max-height' => 2520
        ),
        'adminlang' => array(
            'active' => false,
            'key' => array('vi'),
            'lang' => array(
                'vi' => 'Tiếng Việt',
            )
        ),
        'lang' => array(
            'vi' => 'Tiếng Việt',
            // 'en' => 'English',
        ),
        'lang-doc' => 'vi',
        'slug' => array(
            'vi' => 'Tiếng Việt',
        ),
        'seo' => array(
            'vi' => 'Tiếng Việt',
            // 'en' => 'English',
        ),
        'comlang' => array(
            "gioi-thieu" => array("vi" => "gioi-thieu", "en" => "about-us"),
            "san-pham" => array("vi" => "san-pham", "en" => "product"),
            "tin-tuc" => array("vi" => "tin-tuc", "en" => "news"),
            "tuyen-dung" => array("vi" => "tuyen-dung", "en" => "recruitment"),
            "thu-vien-anh" => array("vi" => "thu-vien-anh", "en" => "gallery"),
            "video" => array("vi" => "video", "en" => "video"),
            "lien-he" => array("vi" => "lien-he", "en" => "contact")
        )
    ),
    'order' => array(
        'ship' => false
    ),
    'login' => array(
        'admin' => 'LoginAdmin' . NN_CONTRACT,
        'member' => 'LoginMember' . NN_CONTRACT,
        'attempt' => 5,
        'delay' => 15
    ),
    'googleAPI' => array(
        'recaptcha' => array(
            'active' => false,
            'urlapi' => 'https://www.google.com/recaptcha/api/siteverify',
            'sitekey' => '',
            'secretkey' => ''
        )
    ),
    'oneSignal' => array(
        'active' => false,
        'id' => 'af12ae0e-cfb7-41d0-91d8-8997fca889f8',
        'restId' => 'MWFmZGVhMzYtY2U0Zi00MjA0LTg0ODEtZWFkZTZlNmM1MDg4'
    )
);

/* Error reporting */
error_reporting(($config['website']['error-reporting']) ? E_ALL : 0);
if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
    $http = 'https://';
} else {
    $http = 'http://';
}
/* CheckSSL */
if (count($config['arrayDomainSSL'])) {
    include LIBRARIES . "checkSSL.php";
}
/* Cấu hình base */
$configUrl = $config['database']['server-name'] . $config['database']['url'];
$configBase = $http . $configUrl;

/* Token */
define('TOKEN', md5(NN_CONTRACT . $config['database']['url']));

/* Path */
define('ROOT', str_replace(basename(__DIR__), '', __DIR__));
define('ASSET', $http . $configUrl);
define('ADMIN', 'admin');

/* Cấu hình login */
$loginAdmin = $config['login']['admin'];
$loginMember = $config['login']['member'];
$config['passkey'] = require LIBRARIES . 'passkey/config.php';

/* Cấu hình upload */
require_once LIBRARIES . "constant.php";
