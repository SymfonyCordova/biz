<?php


namespace Zler\Biz\Context;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Zler\Biz\Dao\DaoProxy;
use Zler\Biz\Dao\FieldSerializer;

class Biz extends Container
{
    protected $providers = array();

    public function __construct(array $values = [])
    {
        parent::__construct($values);

        $this['migration.directories'] = new \ArrayObject();

        $this['autoload.aliases'] = new \ArrayObject(array('' => 'Biz'));

        $this['dao.serializer'] = function () {
            return new FieldSerializer();
        };

        $this['autoload.object_maker.service'] = function ($biz) {
            return function ($namespace, $name) use ($biz) {
                $class = "{$namespace}\\Service\\Impl\\{$name}Impl";

                return new $class($biz);
            };
        };

        $this['autoload.object_maker.dao'] = function ($biz) {
            return function ($namespace, $name) use ($biz) {
                $class = "{$namespace}\\Dao\\Impl\\{$name}Impl";

                return new DaoProxy(new $class($biz), $biz['dao.serializer']);
            };
        };

        $this['autoloader'] = function ($biz) {
            return new ContainerAutoloader(
                $biz,
                $biz['autoload.aliases'],
                array(
                    'service' => $biz['autoload.object_maker.service'],
                    'dao' => $biz['autoload.object_maker.dao'],
                )
            );
        };

        $this['dispatcher'] = function () {
            return new EventDispatcher();
        };

        foreach ($values as $key => $value) {
            $this->offsetSet($key, $value);
        }
    }

    public function register(ServiceProviderInterface $provider, array $values = array())
    {
        $this->providers[] = $provider;

        parent::register($provider, $values);

        return $this;
    }

    public function service($alias)
    {
        return $this['autoloader']->autoload('service', $alias);
    }

    public function dao($alias)
    {
        return $this['autoloader']->autoload('dao', $alias);
    }

}