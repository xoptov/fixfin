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

    /** @var \DateTime */
    private $paidUp;

    /** @var bool */
    private $paid;

    const STATUS_NOT_PAID = 0;
    const STATUS_PAID = 1;

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

    const STATUS_NOT_SUBSCRIBED = 0;
    const STATUS_SUBSCRIBED = 1;

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
        $this->paid = false;
        $this->subscribed = true;
    }

    public static function getPaidLabels()
    {
        return [
            self::STATUS_NOT_PAID => 'entity.status.no',
            self::STATUS_PAID => 'entity.status.yes'
        ];
    }

    public static function getSubscribedLabels()
    {
        return [
            self::STATUS_NOT_SUBSCRIBED => 'entity.status.no',
            self::STATUS_SUBSCRIBED => 'entity.status.yes'
        ];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @param bool $value
     * @return Ticket
     */
    public function setSubscribed($value)
    {
        $this->subscribed = $value;

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
