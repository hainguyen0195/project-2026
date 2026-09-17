<?php

return [
    'default' => 'tin-tuc',
    'defaults' => [
        'category_depth' => 2,
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
            'copy' => true,
        ],
        'statuses' => [
            'is_active' => ['vi' => 'Hiển thị', 'en' => 'Visible'],
            'is_featured' => ['vi' => 'Nổi bật', 'en' => 'Featured'],
            'is_new' => ['vi' => 'Tin mới', 'en' => 'New'],
        ],
    ],
    'types' => [
        'tin-tuc' => [
            'label' => ['vi' => 'Tin tức', 'en' => 'News'],
            'singular' => ['vi' => 'bài viết', 'en' => 'article'],
        ],
    ],
];
