<?php


namespace Zler\Biz\Provider;


use Alipay\EasySDK\Kernel\Config;
use Alipay\EasySDK\Kernel\Factory;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class AlipayEasySdkServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $app['alipay.default_options'] = [
            'protocol' => 'https',
            'gatewayHost' => 'openapi.alipay.com',
            'signType' => 'RSA2',
            'appId' => '',
            'merchantPrivateKey' => '', //应用私钥 MIIEvQIBADANB
            'alipayCertPath' => '',  //支付宝公钥证书文件路径 /foo/alipayCertPublicKey_RSA2.crt
            'alipayRootCertPath' => '', //支付宝根证书文件路径 /foo/alipayRootCert.crt
            'merchantCertPath' => '', //应用公钥证书文件路径 /foo/appCertPublicKey_2019051064521003.crt
            //如果采用非证书模式，则无需赋值上面的三个证书路径，改为赋值如下的支付宝公钥字符串即可
            'alipayPublicKey' => '', //支付宝公钥 MIIBIjANBg
            'notifyUrl' => '', //异步通知接收服务地址
            'encryptKey' => '', //可设置AES密钥
        ];

        $app['alipay.options.initializer'] = $app->protect(function () use ($app) {
            static $initialized = false;

            if ($initialized) {
                return;
            }

            $initialized = true;

            if (!isset($app['alipay.options'])) {
                $app['alipay.options'] = $app['alipay.default_options'];
            }

            $tmp = $app['alipay.options'];
            foreach ($tmp as $name => &$options) {
                $options = array_replace($app['alipay.default_options'], $options);
            }

            $app['alipay.options'] = $tmp;
        });

        $app['alipay.easy.options'] = function ($app){
            $app['alipay.options.initializer']();

            $options = new Config();
            $options->protocol = $app['protocol'];
            $options->gatewayHost = $app['gatewayHost'];
            $options->signType = $app['signType'];
            $options->appId = $app['appId'];
            $options->merchantPrivateKey = $app['merchantPrivateKey'];
            $options->alipayCertPath = $app['alipayCertPath'];
            $options->alipayRootCertPath = $app['alipayRootCertPath'];
            $options->merchantCertPath = $app['merchantCertPath'];
            $options->alipayPublicKey = $app['alipayPublicKey'];
            $options->notifyUrl = $app['notifyUrl'];
            $options->encryptKey = $app['encryptKey'];

            return $options;
        };

        $app['alipay.easy.factory'] = function ($app){
            return Factory::setOptions($app['alipay.easy.options']);
        };
    }
}