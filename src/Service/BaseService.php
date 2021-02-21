<?php


namespace Zler\Biz\Service;


use Zler\Biz\Context\Biz;

abstract class BaseService
{
    protected $biz;

    public function __construct(Biz $biz)
    {
        $this->biz = $biz;
    }
}