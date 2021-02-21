<?php


namespace Zler\Biz\Laravel\Provider;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use Zler\Biz\Context\Biz;
use Zler\Biz\Provider\DoctrineServiceProvider;

class LaravelServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Biz::class , function ($app) {
            $biz = new Biz(config('zler.options'));
            $biz->register(new DoctrineServiceProvider());
            return $biz;
        });
    }

    /**
     * 启动应用服务
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/zler.php' => config_path('zler.php'),
        ]);

        //$this->loadRoutesFrom(__DIR__ . '/../../routes/routes.php');

        //$this->loadMigrationsFrom(__DIR__.'/path/to/migrations');
    }

    /**
     * 获取由提供者提供的服务。
     *
     * @return array
     */
    public function provides()
    {
        return array(Biz::class);
    }
}