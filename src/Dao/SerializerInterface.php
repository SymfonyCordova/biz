<?php


namespace Zler\Biz\Dao;


interface SerializerInterface
{
    public function serialize($method, $value);

    public function unserialize($method, $value);
}