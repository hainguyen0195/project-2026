<?php

return [
    'currency' => 'VND',
    'default_status' => 'pending',
    'default_payment_status' => 'unpaid',
    'statuses' => [
        'pending' => ['vi' => 'Chờ xác nhận', 'en' => 'Pending'],
        'confirmed' => ['vi' => 'Đã xác nhận', 'en' => 'Confirmed'],
        'shipping' => ['vi' => 'Đang giao', 'en' => 'Shipping'],
        'completed' => ['vi' => 'Hoàn tất', 'en' => 'Completed'],
        'cancelled' => ['vi' => 'Đã hủy', 'en' => 'Cancelled'],
    ],
    'transitions' => [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['shipping', 'cancelled'],
        'shipping' => ['completed', 'cancelled'],
        'completed' => [],
        'cancelled' => [],
    ],
    'payment_methods' => [
        'cod' => ['vi' => 'Thanh toán khi nhận hàng', 'en' => 'Cash on delivery'],
        'bank_transfer' => ['vi' => 'Chuyển khoản', 'en' => 'Bank transfer'],
    ],
    'payment_statuses' => [
        'unpaid' => ['vi' => 'Chưa thanh toán', 'en' => 'Unpaid'],
        'paid' => ['vi' => 'Đã thanh toán', 'en' => 'Paid'],
        'refunded' => ['vi' => 'Đã hoàn tiền', 'en' => 'Refunded'],
    ],
    'payment_transitions' => ['unpaid' => ['paid'], 'paid' => ['refunded'], 'refunded' => []],
];
