<?php


namespace Zler\Biz\Laravel\Provider;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
            $biz['migration.directories'][] = dirname(__DIR__).'/migrations';
            $biz = new Biz(config('zler-biz.options'));
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
            __DIR__ . '/../config/zler-biz.php' => config_path('zler-biz.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                \Zler\Biz\Laravel\Command\ScaffoldCommand::class,
            ]);
        }

        foreach ($this->app->tagged('zler.event.subscriber') as $subscriber){
            $this->getDispatcher()->addSubscriber($subscriber);
        }
        //$this->getDispatcher()->addSubscriber();

        //$this->loadRoutesFrom(__DIR__ . '/../../routes/routes.php');

        //$this->loadMigrationsFrom(__DIR__.'/path/to/migrations');
    }

    /**
     * @return Biz
     */
    private function getBiz()
    {
        return $this->app->make(Biz::class);
    }

    /**
     * @return EventDispatcherInterface
     */
    private function getDispatcher()
    {
        $biz = $this->getBiz();
        
        return $biz['dispatcher'];
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