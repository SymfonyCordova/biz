<?php
return [
     'options' => [
        'db.options' => [
            'dbname' => getenv('DB_DATABASE', ''),
            'user' => getenv('DB_USERNAME',''),
            'password' => getenv('DB_PASSWORD', '') ,
            'host' => getenv('DB_HOST', '127.0.0.1'),
            'port' => getenv('DB_PORT', '3306'),
            'driver' => 'pdo_mysql',
            'charset' => 'utf8',
        ],
        'wechat.options' => [
            'gzh' => [
                'app_id' => getenv('gzh_app_id',''),
                'app_secret' => getenv('gzh_app_secret', ''),
                'token' => getenv('gzh_token', ''),
            ],
            'miniprograme' => [
                'app_id' => getenv('miniprograme_app_id', ''),
                'app_secret' => getenv('miniprograme_app_secret', ''),
            ],
            'pc' => [
                'app_id' => getenv('', ''),
                'app_secret' => getenv('', ''),
            ]
        ]
    ]
];
