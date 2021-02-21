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
            'charset' => 'utf8',
        ],
        'wechat.options' => [
            'gzh' => [
                'app_id' => env('gzh_app_id',''),
                'app_secret' => env('gzh_app_secret', ''),
                'token' => env('gzh_token', ''),
            ],
            'miniprograme' => [
                'app_id' => env('miniprograme_app_id', ''),
                'app_secret' => env('miniprograme_app_secret', ''),
            ],
            'pc' => [
                'app_id' => env('', ''),
                'app_secret' => env('', ''),
            ]
        ]
    ]
];
