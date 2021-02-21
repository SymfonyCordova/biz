<?php
return array(
     'options' => array(
        'db.options' => array(
            'dbname' => env('DB_DATABASE', ''),
            'user' => env('DB_USERNAME',''),
            'password' => env('DB_PASSWORD', '') ,
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'driver' => 'pdo_mysql',
            'charset' => 'utf8',
        ),

    ),
);
