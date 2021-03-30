<?php


namespace Zler\Biz\Provider;


use AlibabaCloud\Client\AlibabaCloud;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class AlibabaCloudClientProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['alibaba.cloud.client.default_options'] = [
            'accessKeyId' => '',
            'accessKeySecret' => '',
            'regionId' => 'cn-hangzhou',
        ];

        $app['alibaba.cloud.client.options.initializer'] = $app->protect(function () use ($app) {
            static $initialized = false;

            if ($initialized) {
                return;
            }

            $initialized = true;

            if (!isset($app['alibaba.cloud.client.options'])) {
                $app['alibaba.cloud.client.options'] = $app['alibaba.cloud.client.default_options'];
            }

            $app['alibaba.cloud.client.options'] = array_replace($app['alibaba.cloud.client.default_options'], $app['alibaba.cloud.client.options']);
        });

        $app['alibaba.cloud.client'] = function ($app){
            $app['alibaba.cloud.client.options.initializer']();

            $options = $app['alibaba.cloud.client.options'];

            return AlibabaCloud::accessKeyClient($options['accessKeyId'], $options['accessKeySecret'])
                            ->regionId($options['regionId'])
                            ->asDefaultClient();
        };
    }
}