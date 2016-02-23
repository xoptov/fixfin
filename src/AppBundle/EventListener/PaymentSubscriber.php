<?php

namespace AppBundle\EventListener;

use AppBundle\Exception\DuplicateConfirmException;
use AppBundle\Service\Cashier;
use Doctrine\ORM\NoResultException;
use Monolog\Logger;
use PerfectMoneyBundle\Event\PaymentEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PaymentSubscriber implements EventSubscriberInterface
{
    /** @var Cashier */
    private $cashier;

    /** @var Logger */
    private $logger;

    public static function getSubscribedEvents()
    {
        return array(
            PaymentEvent::CONFIRMED => 'onConfirmed'
        );
    }

    /**
     * @param Cashier $cashier
     */
    public function __construct(Cashier $cashier, Logger $logger)
    {
        $this->cashier = $cashier;
        $this->logger = $logger;
    }

    /**
     * @param PaymentEvent $event
     */
    public function onConfirmed(PaymentEvent $event)
    {
        try {
            $this->cashier->handlePayment($event->getResult());
        } catch (NoResultException $e) {
            $this->logger->error($e->getMessage());
        } catch (DuplicateConfirmException $e) {
            $this->logger->error($e->getMessage());
        }
    }
}