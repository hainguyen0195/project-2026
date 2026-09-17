<!-- Basehref -->
<base href="<?= $configBase ?>" />

<!-- UTF-8 -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<?php
$metaTitle = trim((string)$seo->get('title'));
$metaDescription = trim(strip_tags((string)$seo->get('description')));
if ($metaTitle === '') $metaTitle = trim((string)($setting['name' . $lang] ?? $setting['namevi'] ?? 'Website'));
if ($metaDescription === '') {
    $companyName = trim((string)($setting['name' . $lang] ?? $setting['namevi'] ?? ''));
    $companyAddress = trim(strip_tags((string)($optsetting['address'] ?? '')));
    $metaDescription = trim($companyName . ($companyAddress !== '' ? ' - ' . $companyAddress : ''));
}
?>
<!-- Title, Keywords, Description -->
<title><?= htmlspecialchars($metaTitle, ENT_QUOTES, 'UTF-8') ?></title>
<meta name="keywords" content="<?= $seo->get('keywords') ?>" />
<meta name="description" content="<?= htmlspecialchars(mb_substr($metaDescription, 0, 160), ENT_QUOTES, 'UTF-8') ?>" />

<!-- Robots -->
<meta name="robots" content="<?= (in_array($source, $config['website']['noseo'])) ? 'noindex,nofollow' : 'index,follow,noodp' ?>" />

<!-- Favicon -->
<link href="<?= ASSET . THUMBS . '/48x48x2/' . UPLOAD_PHOTO_L . $favicon['photo'] ?>" rel="shortcut icon" type="image/x-icon" />

<!-- Webmaster Tool -->
<?= $func->decodeHtmlChars($setting['mastertool']) ?>

<!-- GEO -->
<meta name="geo.region" content="VN" />
<meta name="geo.placename" content="Hồ Chí Minh" />
<meta name="geo.position" content="10.823099;106.629664" />
<meta name="ICBM" content="10.823099, 106.629664" />

<!-- Author - Copyright -->
<meta name='revisit-after' content='1 days' />
<meta name="author" content="<?= $setting['name' . $lang] ?>" />
<meta name="copyright" content="<?= $setting['name' . $lang] . " - [" . $optsetting['email'] . "]" ?>" />

<!-- Facebook -->
<meta property="og:type" content="<?= $seo->get('type') ?>" />
<meta property="og:site_name" content="<?= $setting['name' . $lang] ?>" />
<meta property="og:title" content="<?= $seo->get('title') ?>" />
<meta property="og:description" content="<?= $seo->get('description') ?>" />
<meta property="og:url" content="<?= $seo->get('url') ?>" />
<meta property="og:image" content="<?= $seo->get('photo') ?>" />
<meta property="og:image:alt" content="<?= $seo->get('title') ?>" />
<meta property="og:image:type" content="<?= $seo->get('photo:type') ?>" />
<meta property="og:image:width" content="<?= $seo->get('photo:width') ?>" />
<meta property="og:image:height" content="<?= $seo->get('photo:height') ?>" />

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:site" content="<?= $optsetting['email'] ?>" />
<meta name="twitter:creator" content="<?= $setting['name' . $lang] ?>" />
<meta property="og:url" content="<?= $seo->get('url') ?>" />
<meta property="og:title" content="<?= $seo->get('title') ?>" />
<meta property="og:description" content="<?= $seo->get('description') ?>" />
<meta property="og:image" content="<?= $seo->get('photo') ?>" />

<!-- Canonical -->
<link rel="canonical" href="<?= $func->getCurrentPageURL() ?>" />

<!-- Chống đổi màu trên IOS -->
<meta name="format-detection" content="telephone=no">

<!-- Viewport -->

<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,500&display=swap" rel="stylesheet">
