<?php

return [
    'default' => 'san-pham',
    'defaults' => [
        'category_depth' => 4,
        'features' => [
            'code' => true,
            'pricing' => true,
            'rating' => true,
            'brand' => true,
            'size' => true,
            'description' => true,
            'content' => true,
            'specifications' => true,
            'images' => true,
            'seo' => true,
            'schema' => true,
            'ai_seo' => true,
            'copy' => true,
        ],
        'statuses' => [
            'is_active' => ['vi' => 'Hiển thị', 'en' => 'Visible'],
            'is_featured' => ['vi' => 'Nổi bật', 'en' => 'Featured'],
            'is_new' => ['vi' => 'Mới', 'en' => 'New'],
            'is_bestseller' => ['vi' => 'Bán chạy', 'en' => 'Bestseller'],
        ],
    ],
    'types' => [
        'san-pham' => [
            'label' => ['vi' => 'Sản phẩm', 'en' => 'Products'],
            'singular' => ['vi' => 'sản phẩm', 'en' => 'product'],
        ],
        'thu-vien-anh' => [
            'label' => ['vi' => 'Thư viện ảnh', 'en' => 'Photo library'],
            'singular' => ['vi' => 'bộ ảnh', 'en' => 'photo collection'],
            'category_depth' => 1,
            'features' => [
                'code' => false,
                'pricing' => false,
                'rating' => false,
                'brand' => false,
                'size' => false,
                'specifications' => false,
                'schema' => false,
                'ai_seo' => false,
            ],
            'statuses' => [
                'is_active' => ['vi' => 'Hiển thị', 'en' => 'Visible'],
                'is_featured' => ['vi' => 'Nổi bật', 'en' => 'Featured'],
            ],
        ],
    ],
];
