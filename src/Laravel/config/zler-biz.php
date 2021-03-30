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
         'alipay.options' => [
             'protocol' => 'https',
             'gatewayHost' => 'openapi.alipay.com',
             'signType' => 'RSA2',
             'appId' => env('ALIPAY_APP_ID', ''),
             'merchantPrivateKey' => env('ALIPAY_APP_PRIVATE_KEY', ''), //应用私钥 MIIEvQIBADANB
             'alipayCertPath' => env('ALIPAY_CERT_PATH', ''),  //支付宝公钥证书文件路径 /foo/alipayCertPublicKey_RSA2.crt
             'alipayRootCertPath' => env('ALIPAY_ROOT_CERT_PATH',''), //支付宝根证书文件路径 /foo/alipayRootCert.crt
             'merchantCertPath' => env('ALIPAY_APP_PUBLISH_CERT_PATH',''), //应用公钥证书文件路径 /foo/appCertPublicKey_2019051064521003.crt
             //如果采用非证书模式，则无需赋值上面的三个证书路径，改为赋值如下的支付宝公钥字符串即可
             'alipayPublicKey' => env('ALIPAY_PUBLISH_KEY'), //支付宝公钥 MIIBIjANBg
             'notifyUrl' => '', //异步通知接收服务地址
             'encryptKey' => '', //可设置AES密钥
         ],
         'alibaba.cloud.client.options' => [
             'accessKeyId' => env('ALIBABA_CLOUD_ACCESS_KEY_ID', ''),
             'accessKeySecret' => env('ALIBABA_CLOUD_ACCESS_KEY_SECRET', ''),
             'regionId' => env('ALIBABA_CLOUD_REGION_ID', ''),
         ],
    ]
];
