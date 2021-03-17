<?php


namespace Zler\Biz\Dao;


use Zler\Biz\Context\Biz;

abstract class WeiXinAppPayGeneralDaoImpl implements WeiXinAppPayGeneralDaoInterface
{
    protected $biz;

    public function __construct(Biz $biz)
    {
        $this->biz = $biz;
    }
}