<?php
return [
     'options' => [
        'db.options' => [
            'dbname' => env('DB_DATABASE', ''),
            'user' => env('DB_USERNAME',''),
            'password' => env('DB_PASSWORD', '') ,
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'driver' => 'pdo_mysql',
            'charset' => 'utf8mb4',
        ],
         'wechat.pay.options' => [
             'merchant_id' => env('MERCHANT_ID', ''), //商户号
             'merchant_serial_number' => env('MERCHANT_SERIAL_NUMBER', ''), //商户API证书序列号
             'merchant_private_key_path' => env('MERCHANT_PRIVATE_KEY_PATH', ''), //商户私钥
             'wechat_pay_certificate_path' => env('WECHAT_PAY_CERTIFICATE_PATH', ''), //微信支付平台证书
        ],
    ]
];
