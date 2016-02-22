<?php

namespace AppBundle\EventListener;

use AppBundle\Event\TicketEvent;
use AppBundle\Entity\Rate;
use AppBundle\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class TicketSubscriber implements EventSubscriberInterface
{
    /** @var PropertyAccessor */
    private $accessor;

    /**
     * @param PropertyAccessor $accessor
     */
    public function __construct(PropertyAccessor $accessor)
    {
        $this->accessor = $accessor;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            TicketEvent::TABLE_OPENED => 'onTableOpened'
        );
    }

    /**
     * @param TicketEvent $event
     */
    public function onTableOpened(TicketEvent $event)
    {
        /**
         * @var User $user
         * @var Rate $rate
         */
        $rate = $this->accessor->getValue($event, 'ticket.rate');
        $user = $this->accessor->getValue($event, 'ticket.user');

        if ($rate->getScore()) {
            $user->increaseScore($rate->getScore());
        }
    }
}