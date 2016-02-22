<?php

namespace AppBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use AppBundle\Service\Banker;
use Symfony\Component\HttpKernel\KernelEvents;

class RewardPaymentSubscriber implements EventSubscriberInterface
{
    /** @var Banker */
    private $banker;

    public function __construct(Banker $banker)
    {
        $this->banker = $banker;
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::TERMINATE => 'onTerminate'
        );
    }

    public function onTerminate()
    {
        $this->banker->makeRewardPayments();
    }
}