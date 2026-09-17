<?php
$social_template = $optsetting['social_template'] ?? '1';
$social_template_device = $optsetting['social_template_device'] ?? 'all';
$social_template_position = $optsetting['social_template_position'] ?? 'left';
$social_template_offset_bottom = $optsetting['social_template_offset_bottom'] ?? '';
$social_template_offset_side = $optsetting['social_template_offset_side'] ?? '';

$show_social = true;

if ($social_template_device == 'pc' && $deviceType != 'computer') {
    $show_social = false;
} elseif ($social_template_device == 'mobile' && $deviceType == 'computer') {
    $show_social = false;
}

if ($show_social && !empty($social)) {
    if ($social_template == '2') {
        include TEMPLATE . LAYOUT . "social_tpl2.php";
    } elseif ($social_template == '3') {
        include TEMPLATE . LAYOUT . "social_tpl3.php";
    } elseif ($social_template == '4') {
        include TEMPLATE . LAYOUT . "social_tpl4.php";
    } elseif ($social_template == '5') {
        include TEMPLATE . LAYOUT . "social_tpl5.php";
    }
}
?>
