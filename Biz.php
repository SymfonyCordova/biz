<?php

use Zler\Biz\Context\Biz;

$options = array(
    'db.options' => array(
        'dbname' => getenv('DB_NAME') ?: 'biz-framework',
        'user' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASSWORD') ?: '',
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: 3306,
        'driver' => 'pdo_mysql',
        'charset' => 'utf8',
    )
);

$biz = new Biz($options);

$biz->register(new \Zler\Biz\Provider\DoctrineServiceProvider());

return $biz;

