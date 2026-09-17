<?php

return [
    'types' => [
        'logo' => ['label' => ['vi' => 'Logo', 'en' => 'Logo'], 'singleton' => true, 'width' => 205, 'height' => 95, 'fields' => []],
        'logo-footer' => ['label' => ['vi' => 'Logo footer', 'en' => 'Footer logo'], 'singleton' => true, 'width' => 310, 'height' => 185, 'fields' => []],
        'favicon' => ['label' => ['vi' => 'Favicon', 'en' => 'Favicon'], 'singleton' => true, 'width' => 48, 'height' => 48, 'fields' => []],
        'dmca' => ['label' => ['vi' => 'DMCA', 'en' => 'DMCA'], 'singleton' => true, 'width' => 200, 'height' => 50, 'fields' => ['link']],
        'bct' => ['label' => ['vi' => 'Bộ Công Thương', 'en' => 'Trade certification'], 'singleton' => true, 'width' => 200, 'height' => 50, 'fields' => ['link']],
        'popup' => ['label' => ['vi' => 'Popup', 'en' => 'Popup'], 'singleton' => true, 'width' => 800, 'height' => 530, 'fields' => ['name', 'link']],
        'slide' => ['label' => ['vi' => 'Slideshow', 'en' => 'Slideshow'], 'singleton' => false, 'width' => 1284, 'height' => 756, 'fields' => ['name', 'link', 'text1', 'text2', 'texts']],
        'social' => ['label' => ['vi' => 'Mạng xã hội', 'en' => 'Social links'], 'singleton' => false, 'width' => 30, 'height' => 30, 'fields' => ['name', 'link']],
        'social-footer' => ['label' => ['vi' => 'Mạng xã hội footer', 'en' => 'Footer social links'], 'singleton' => false, 'width' => 30, 'height' => 30, 'fields' => ['name', 'link']],
    ],
];
