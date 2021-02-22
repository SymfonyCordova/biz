<?php


namespace Zler\Biz\Service\Exception;


class ServiceException extends \RuntimeException
{
    public function __construct($message, $code = 500, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode(){
        return $this->code;
    }
}