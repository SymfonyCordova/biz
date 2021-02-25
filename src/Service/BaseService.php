<?php


namespace Zler\Biz\Service;


use Zler\Biz\Context\Biz;
use Zler\Biz\Dao\Connection;
use Zler\Biz\Service\Exception\AccessDeniedException;
use Zler\Biz\Service\Exception\NotFoundException;
use Zler\Biz\Service\Exception\ServiceException;

abstract class BaseService
{
    protected $biz;

    public function __construct(Biz $biz)
    {
        $this->biz = $biz;
    }

    /**
     * @return Biz
     */
    protected function getBiz()
    {
        return $this->biz;
    }

    protected function createService($alias)
    {
        return $this->getBiz()->service($alias);
    }

    protected function createServiceException($message = 'Service Exception')
    {
        return new ServiceException($message);
    }

    protected function createAccessDeniedException($message = 'Access Denied')
    {
        return new AccessDeniedException($message);
    }

    protected function createNotFoundException($message = 'Not Found')
    {
        return new NotFoundException($message);
    }

    /**
     * @return Connection
     */
    protected function db()
    {
        $biz = $this->getBiz();

        return $biz['db'];
    }

    protected function beginTransaction()
    {
        $this->db()->beginTransaction();
    }

    protected function commit()
    {
        $this->db()->commit();
    }

    protected function rollback()
    {
        $this->db()->rollBack();
    }
}