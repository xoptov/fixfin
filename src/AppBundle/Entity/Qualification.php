<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Qualification
{
    /** @var int */
    private $id;

    /** @var Ticket */
    private $ticket;

    /** @var Ticket[] */
    private $transferredTickets;

    /** @var bool */
    private $passed;

    /** @var int */
    private $requireInvitation;

    public function __construct()
    {
        $this->transferredTickets = new ArrayCollection();
        $this->passed = false;
        $this->requireInvitation = 0;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Ticket $ticket
     * @return Qualification
     */
    public function setTicket(Ticket $ticket)
    {
        $this->ticket = $ticket;

        return $this;
    }

    /**
     * @return Ticket
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * @param Ticket[]|ArrayCollection $tickets
     * @return Qualification
     */
    public function setTransferredTickets($tickets)
    {
        $this->transferredTickets = $tickets;

        return $this;
    }

    /**
     * @return Ticket[]|ArrayCollection
     */
    public function getTransferredTickets()
    {
        return $this->transferredTickets;
    }

    /**
     * @param bool $value
     * @return Qualification
     */
    public function setPassed($value)
    {
        $this->passed = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPassed()
    {
        return $this->passed;
    }

    /**
     * @param int $value
     * @return Qualification
     */
    public function setRequireInvitation($value)
    {
        $this->requireInvitation = $value;

        return $this;
    }

    /**
     * @return int
     */
    public function getRequireInvitation()
    {
        return $this->requireInvitation;
    }
}