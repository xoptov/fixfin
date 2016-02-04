<?php

namespace AppBundle\Event;

use AppBundle\Entity\Ticket;
use Symfony\Component\EventDispatcher\Event;

class TicketEvent extends Event
{
    const CREATED = 'ticket.created';
    const EXPIRED = 'ticket.expired';
    const CHIEF_REESTABLISHED = 'ticket.chief_reestablished';
    const CHIEF_REMOVED = 'ticket.chief_removed';
    const SUBORDINATES_REESTABLISHED = 'ticket.subordinates_reestablished';

    /** @var Ticket */
    private $ticket;

    /**
     * @param Ticket $ticket
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * @return Ticket
     */
    public function getTicket()
    {
        return $this->ticket;
    }
}