<?php

return [
    'defaults' => [
        'singleton' => true,
        'category_depth' => 0,
        'features' => [
            'code' => false,
            'pricing' => false,
            'rating' => false,
            'brand' => false,
            'size' => false,
            'description' => true,
            'content' => true,
            'specifications' => false,
            'images' => true,
            'seo' => true,
            'schema' => false,
            'ai_seo' => true,
            'copy' => false,
        ],
        'statuses' => [
            'is_active' => ['vi' => 'Hiển thị', 'en' => 'Visible'],
        ],
    ],
    'types' => [
        'gioi-thieu' => [
            'label' => ['vi' => 'Giới thiệu', 'en' => 'About us'],
            'singular' => ['vi' => 'trang giới thiệu', 'en' => 'about page'],
        ],
    ],
];
