<?php

namespace AppBundle\EventListener;

use AppBundle\Service\Cashier;
use PerfectMoneyBundle\Event\PaymentEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use PerfectMoneyBundle\Model\PaymentConfirmation;

class PaymentSubscriber implements EventSubscriberInterface
{
    /** @var Cashier */
    private $cashier;

    public static function getSubscribedEvents()
    {
        return array(
            PaymentEvent::CONFIRMED => 'onConfirmed'
        );
    }

    /**
     * @param Cashier $cashier
     */
    public function __construct(Cashier $cashier)
    {
        $this->cashier = $cashier;
    }

    /**
     * @param PaymentEvent $event
     */
    public function onConfirmed(PaymentEvent $event)
    {
        $this->cashier->handlePayment($event->getResult());
    }
}