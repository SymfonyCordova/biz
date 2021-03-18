<?php


namespace Zler\Biz\Provider;


use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use WechatPay\GuzzleMiddleware\Util\PemUtil;
use WechatPay\GuzzleMiddleware\WechatPayMiddleware;

class WeChatPayGuzzleMiddlewareServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['wechat.pay.default_options'] = [
            'merchant_id' => '',
            'merchant_serial_number' => '',
            'merchant_private_key_path' => '',
            'wechat_pay_certificate_path' => '',
        ];

        $app['wechat.pay.options.initializer'] = $app->protect(function () use ($app) {
            static $initialized = false;

            if ($initialized) {
                return;
            }

            $initialized = true;

            if (!isset($app['wechat.pay.options'])) {
                $app['wechat.pay.options'] = $app['wechat.pay.default_options'];
            }

            $tmp = $app['wechat.pay.options'];
            foreach ($tmp as $name => &$options) {
                $options = array_replace($app['wechat.pay.default_options'], $options);
            }

            $app['wechat.pay.options'] = $tmp;
        });

        $app['wechat.pay.guzzle.middleware.client'] = function ($app){
            $app['wechat.pay.options.initializer']();

            $wechatPayOptions = $app['wechat.pay.options'];

            //商户相关配置
            $merchantId = $wechatPayOptions['merchant_id'];// 商户号
            $merchantSerialNumber = $wechatPayOptions['merchant_serial_number'];//商户API证书序列号
            $merchantPrivateKey = PemUtil::loadPrivateKey($wechatPayOptions['merchant_private_key_path']);//商户aip证书私钥
            //微信支付平台配置
            $wechatPayCertificate = PemUtil::loadCertificate($wechatPayOptions['wechat_pay_certificate_path']);//微信支付平台证书

            //构造一个WechatPayMiddleware
            $wechatpayMiddleware = WechatPayMiddleware::builder()
                ->withMerchant($merchantId, $merchantSerialNumber, $merchantPrivateKey) // 传入商户相关配置
                ->withWechatPay([ $wechatPayCertificate ]) // 可传入多个微信支付平台证书，参数类型为array
                ->build();

            //将WechatPayMiddleware添加到Guzzle的HandlerStack中
            $stack = HandlerStack::create();
            $stack->push($wechatpayMiddleware, 'wechatpay');

            //创建Guzzle HTTP Client时，将HandlerStack传入
            return new Client(['handler' => $stack]);
        };
    }
}