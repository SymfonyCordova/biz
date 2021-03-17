<?php


namespace Zler\Biz\Dao;


interface WeiXinAppPayGeneralDaoInterface
{
    const UNIFIED_ORDER_URL = 'https://api.mch.weixin.qq.com/v3/pay/transactions/app';

    public function unifiedOrder(array $fields);
}