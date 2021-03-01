<?php


namespace Zler\Biz\Event;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zler\Biz\Context\Biz;

abstract class EventSubscriber implements EventSubscriberInterface
{
    /**
     * @var Biz
     */
    private $biz;

    public function __construct(Biz $biz)
    {
        $this->biz = $biz;
    }

    /**
     * @return Biz
     */
    public function getBiz()
    {
        return $this->biz;
    }
}