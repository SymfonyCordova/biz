<?php


namespace Zler\Biz\Dao;


interface AdvancedDaoInterface extends GeneralDaoInterface
{
    public function batchCreate($rows);

    public function batchUpdate($identifies, $updateColumnsList, $identifyColumn = 'id');
}