<?php

return [
    'defaults' => [
        'category_depth' => 0,
        'features' => [
            'code' => false,
            'pricing' => false,
            'rating' => false,
            'brand' => false,
            'size' => false,
            'description' => false,
            'content' => false,
            'specifications' => false,
            'images' => true,
            'seo' => true,
            'schema' => false,
            'ai_seo' => false,
            'copy' => false,
        ],
        'statuses' => [],
    ],
    'types' => [
        'seo-trang-chu' => ['label' => ['vi' => 'Trang chủ', 'en' => 'Home']],
        'seo-san-pham' => ['label' => ['vi' => 'Sản phẩm', 'en' => 'Products']],
        'seo-tin-tuc' => ['label' => ['vi' => 'Tin tức', 'en' => 'News']],
        'seo-du-an' => ['label' => ['vi' => 'Dự án', 'en' => 'Projects']],
        'seo-tuyen-dung' => ['label' => ['vi' => 'Tuyển dụng', 'en' => 'Careers']],
        'seo-lien-he' => ['label' => ['vi' => 'Liên hệ', 'en' => 'Contact']],
    ],
];
