<?php
$phone_template = $optsetting['phone_template'] ?? '1';
$phone_template_device = $optsetting['phone_template_device'] ?? 'all';

$show_phone_button = true;

// $deviceType is usually 'computer', 'phone', 'tablet'
if ($phone_template_device == 'pc' && $deviceType != 'computer') {
    $show_phone_button = false;
} elseif ($phone_template_device == 'mobile' && $deviceType == 'computer') {
    $show_phone_button = false;
}

if ($show_phone_button) {
    if ($phone_template == '2') {
        include TEMPLATE . LAYOUT . "phone_tpl2.php";
    } elseif ($phone_template == '3') {
        include TEMPLATE . LAYOUT . "phone_tpl3.php";
    } else {
        include TEMPLATE . LAYOUT . "phone_tpl1.php";
    }
}
?>
