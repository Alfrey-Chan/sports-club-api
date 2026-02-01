<?php

return [
    'secret' => env('STRIPE_SECRET'),
    'prices' => [
        '2_per_week' => env('STRIPE_PRICE_2W'),
        '3_per_week' => env('STRIPE_PRICE_3W'),
        '4_per_week' => env('STRIPE_PRICE_4W'),
        '5_per_week' => env('STRIPE_PRICE_5W'),
    ],
    'frontend_url' => env('FRONTEND_URL', 'http://localhost:5173/'),
    'test_customer' => env('STRIPE_TEST_CUSTOMER'),
];
