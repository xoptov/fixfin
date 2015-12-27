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

    /**
     * @var int
     * @deprecated
     */
    private $type;

    /** @var \DateTime */
    private $paidUp;

    /** @var bool */
    private $paid;

    /** @var Ticket */
    private $chiefTicket;

    /** @var Invoice[] */
    private $invoices;

    /** @var User */
    private $user;

    /** @var Rate */
    private $rate;

    /** @var bool */
    private $subscribed;

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
        $this->type = 0;
        $this->paid = false;
        $this->subscribed = true;
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
     * @deprecated
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return int
     * @deprecated
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

    /**
     * @param User $user
     * @return Ticket
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param Rate $rate
     * @return Ticket
     */
    public function setRate(Rate $rate)
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * @return Rate
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @param bool $subscribed
     * @return Ticket
     */
    public function setSubscribed($subscribed)
    {
        $this->subscribed = $subscribed;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSubscribed()
    {
        return $this->subscribed;
    }
}
