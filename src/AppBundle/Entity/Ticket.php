<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Ticket
 */
class Ticket
{
    /** @var int */
    private $id;

    /** @var string */
    private $title;

    /** @var int */
    private $type;

    /** @var \DateTime */
    private $paidUp;

    /** @var bool */
    private $paid;

    /** @var Ticket */
    private $chiefTicket;

    /** @var ArrayCollection */
    private $payments;

    /** @var User */
    private $user;

    /** @var Rate */
    private $rate;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $title
     * @return Ticket
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param integer $type
     * @return Ticket
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param \DateTime $paidUp
     * @return Ticket
     */
    public function setPaidUp($paidUp)
    {
        $this->paidUp = $paidUp;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPaidUp()
    {
        return $this->paidUp;
    }

    /**
     * @param boolean $paid
     * @return Ticket
     */
    public function setPaid($paid)
    {
        $this->paid = $paid;

        return $this;
    }

    /**
     * @return bool
     */
    public function getPaid()
    {
        return $this->paid;
    }

    /**
     * @param Ticket $ticket
     * @return Ticket
     */
    public function setChiefTicket(Ticket $ticket)
    {
        $this->chiefTicket = $ticket;

        return $this;
    }

    /**
     * @return Ticket
     */
    public function getChiefTicket()
    {
        return $this->chiefTicket;
    }
}

