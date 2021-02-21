<?php


namespace Zler\Biz\Context;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Zler\Biz\Dao\DaoProxy;
use Zler\Biz\Dao\FieldSerializer;

class Biz extends Container
{
    protected $providers = array();

    public function __construct(array $values = [])
    {
        parent::__construct($values);

        $biz = $this;

        $this['dao.serializer'] = function () {
            return new FieldSerializer();
        };

        $biz['autoload.object_maker.service'] = function ($biz) {
            return function ($namespace, $name) use ($biz) {
                $className = "{$namespace}\\Service\\Impl\\{$name}Impl";

                return new $className($biz);
            };
        };

        $biz['autoload.object_maker.dao'] = function ($biz) {
            return function ($namespace, $name) use ($biz) {
                $class = "{$namespace}\\Dao\\Impl\\{$name}Impl";

                return new DaoProxy(new $class($biz), $biz['dao.serializer']);
            };
        };

        $biz['autoloader'] = array(
            'service' => $this['autoload.object_maker.service'],
            'dao' => $this['autoload.object_maker.dao']
        );

        foreach ($values as $key => $value) {
            $this->offsetSet($key, $value);
        }
    }

    protected function autoload($props, $alias){
        $parts = explode(":", $alias);

        if(empty($parts)){
            throw new \InvalidArgumentException("Service alias parameter is invalid.");
        }

        $obj = $this['autoloader'][$props]($parts[0], $parts[1]);

        return $obj;
    }

    public function register(ServiceProviderInterface $provider, array $values = array())
    {
        $this->providers[] = $provider;

        parent::register($provider, $values);

        return $this;
    }

    public function service($alias)
    {
        return $this->autoload('service', $alias);
    }

    public function dao($alias)
    {
        return $this->autoload('dao', $alias);
    }

}